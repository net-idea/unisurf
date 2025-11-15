#!/bin/bash
# convert_ttf_to_web.sh

for ttf in *.ttf; do
    name="${ttf%.ttf}"
    echo "Converting $ttf..."

    # WOFF
    pyftsubset "$ttf" \
        --output-file="$name.woff" \
        --flavor=woff \
        --layout-features=* \
        --unicodes="U+0000-017F,U+2000-206F,U+20A0-20CF,U+2100-214F" \
        --no-hinting

    # WOFF2
    pyftsubset "$ttf" \
        --output-file="$name.woff2" \
        --flavor=woff2 \
        --layout-features=* \
        --unicodes="U+0000-017F,U+2000-206F,U+20A0-20CF,U+2100-214F" \
        --no-hinting
done

echo "Done! Generated .woff and .woff2 files."
