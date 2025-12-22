#!/usr/bin/env bash
# optimize-images.sh
# Recursively optimize JPG/PNG in assets/images and create .webp versions.
# Usage: ./scripts/optimize-images.sh [--quality=80] [--overwrite] [--lossless] [--dry-run] [--dir=assets/images]

set -euo pipefail
IFS=$'\n\t'

QUALITY=80
OVERWRITE=0
DRY_RUN=0
LOSSLESS=0
DIR="assets/images"

print_help() {
  cat <<EOF
Usage: $0 [options]
Options:
  --quality=N       Quality for WebP/JPEG (1-100). Default: ${QUALITY}
  --overwrite       Replace original JPG/PNG with optimized versions (if tools available).
  --lossless        Produce lossless WebP for PNGs (only if supported).
  --dry-run         Only show actions, do not write files.
  --dir=PATH        Directory to scan (default: assets/images)
  --help            Show this help

Examples:
  $0 --quality=85                  # create webp at quality 85
  $0 --quality=85 --overwrite     # create webp and re-encode originals
  $0 --dry-run                    # do not write, just list
EOF
}

# Simple arg parsing
for arg in "$@"; do
  case $arg in
    --quality=* ) QUALITY="${arg#*=}"; shift ;;
    --dir=* ) DIR="${arg#*=}"; shift ;;
    --overwrite ) OVERWRITE=1; shift ;;
    --dry-run ) DRY_RUN=1; shift ;;
    --lossless ) LOSSLESS=1; shift ;;
    --help ) print_help; exit 0; shift ;;
    * ) echo "Unknown arg: $arg"; print_help; exit 1 ;;
  esac
done

# Validate quality
if ! [[ "$QUALITY" =~ ^[0-9]+$ ]] || [ "$QUALITY" -lt 1 ] || [ "$QUALITY" -gt 100 ]; then
  echo "Quality must be integer between 1 and 100" >&2
  exit 2
fi

# Resolve dir
ROOT_DIR="$(cd "${DIR}" 2>/dev/null && pwd || echo "")"
if [ -z "$ROOT_DIR" ] || [ ! -d "$ROOT_DIR" ]; then
  echo "Directory not found: ${DIR}" >&2
  exit 3
fi

# Detect tools
command -v cwebp >/dev/null 2>&1 && CWEBP=1 || CWEBP=0
command -v magick >/dev/null 2>&1 && MAGICK=1 || MAGICK=0
command -v convert >/dev/null 2>&1 && CONVERT=1 || CONVERT=0
command -v pngquant >/dev/null 2>&1 && PNGQUANT=1 || PNGQUANT=0
command -v optipng >/dev/null 2>&1 && OPTIPNG=1 || OPTIPNG=0
command -v jpegoptim >/dev/null 2>&1 && JPEGOPTIM=1 || JPEGOPTIM=0

echo "Optimize images — dir: ${ROOT_DIR} quality: ${QUALITY} overwrite: ${OVERWRITE} lossless: ${LOSSLESS} dry-run: ${DRY_RUN}"

total_processed=0
total_skipped=0
total_errors=0

# Find images
while IFS= read -r -d '' file; do
  ext="${file##*.}"
  ext_lc="${ext,,}"
  if [[ "$ext_lc" != "png" && "$ext_lc" != "jpg" && "$ext_lc" != "jpeg" ]]; then
    total_skipped=$((total_skipped+1))
    continue
  fi

  webp_file="${file%.*}.webp"
  relfile="${file#$(pwd)/}"

  if [ "$DRY_RUN" -eq 1 ]; then
    echo "[dry-run] would convert to webp: ${file} -> ${webp_file}"
    total_processed=$((total_processed+1))
    continue
  fi

  # Convert to webp
  echo "Converting: ${file} -> ${webp_file}"
  if [ "$CWEBP" -eq 1 ]; then
    if [ "$ext_lc" = "png" ] && [ "$LOSSLESS" -eq 1 ]; then
      cwebp -lossless -q "$QUALITY" "$file" -o "$webp_file" 2>/dev/null || { echo "cwebp failed for $file"; total_errors=$((total_errors+1)); continue; }
    else
      cwebp -q "$QUALITY" "$file" -o "$webp_file" 2>/dev/null || { echo "cwebp failed for $file"; total_errors=$((total_errors+1)); continue; }
    fi
  elif [ "$MAGICK" -eq 1 ] || [ "$CONVERT" -eq 1 ]; then
    # Use ImageMagick (magick preferred)
    if [ "$MAGICK" -eq 1 ]; then
      if [ "$ext_lc" = "png" ] && [ "$LOSSLESS" -eq 1 ]; then
        magick "$file" -define webp:lossless=true "$webp_file" || { echo "magick failed for $file"; total_errors=$((total_errors+1)); continue; }
      else
        magick "$file" -quality "$QUALITY" "$webp_file" || { echo "magick failed for $file"; total_errors=$((total_errors+1)); continue; }
      fi
    else
      if [ "$ext_lc" = "png" ] && [ "$LOSSLESS" -eq 1 ]; then
        convert "$file" -define webp:lossless=true "$webp_file" || { echo "convert failed for $file"; total_errors=$((total_errors+1)); continue; }
      else
        convert "$file" -quality "$QUALITY" "$webp_file" || { echo "convert failed for $file"; total_errors=$((total_errors+1)); continue; }
      fi
    fi
  else
    echo "No suitable WebP conversion tool found (cwebp or ImageMagick). Skipping $file" >&2
    total_errors=$((total_errors+1)); continue
  fi

  echo "-> created ${webp_file}"
  total_processed=$((total_processed+1))

  # Optionally overwrite original with optimized version
  if [ "$OVERWRITE" -eq 1 ]; then
    echo "Optimizing original: ${file}"
    if [ "$ext_lc" = "png" ]; then
      if [ "$PNGQUANT" -eq 1 ]; then
        tmp_out="${file}.tmp.png"
        pngquant --quality=${QUALITY}-${QUALITY} --output "$tmp_out" --force "$file" >/dev/null 2>&1 || { echo "pngquant failed for $file"; total_errors=$((total_errors+1)); rm -f "$tmp_out"; continue; }
        mv "$tmp_out" "$file"
        echo "-> pngquant optimized $file"
      elif [ "$OPTIPNG" -eq 1 ]; then
        optipng -o7 "$file" >/dev/null 2>&1 || { echo "optipng failed for $file"; total_errors=$((total_errors+1)); continue; }
        echo "-> optipng optimized $file"
      else
        echo "No PNG optimizer (pngquant/optipng) found; skipping PNG optimization for $file"
      fi
    else
      # jpeg
      if [ "$JPEGOPTIM" -eq 1 ]; then
        jpegoptim --strip-all --max=$QUALITY "$file" >/dev/null 2>&1 || { echo "jpegoptim failed for $file"; total_errors=$((total_errors+1)); continue; }
        echo "-> jpegoptim optimized $file"
      else
        # fallback: re-encode with ImageMagick if available
        if [ "$MAGICK" -eq 1 ]; then
          magick "$file" -quality "$QUALITY" "$file" || { echo "magick re-encode failed for $file"; total_errors=$((total_errors+1)); continue; }
          echo "-> magick re-encoded $file"
        else
          echo "No JPEG optimizer (jpegoptim) or ImageMagick found; skipping JPEG optimization for $file"
        fi
      fi
    fi
  fi

done < <(find "$ROOT_DIR" -type f \( -iname "*.png" -o -iname "*.jpg" -o -iname "*.jpeg" \) -print0)

echo "Done. processed: ${total_processed}, skipped: ${total_skipped}, errors: ${total_errors}"

exit $([ "$total_errors" -gt 0 ] && echo 1 || echo 0)
