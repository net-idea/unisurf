# Secrets and Decryption Keys

DO NOT COMMIT PRODUCTION SECRETS OR DECRYPTION KEYS

The directory `config/secrets/` can contain sensitive material used to encrypt and
decrypt secrets for each environment (for example `config/secrets/prod/prod.decrypt.private.php`).

⚠️ CAUTION: DO NOT COMMIT THE DECRYPTION KEY FOR THE PROD ENVIRONMENT ⚠️

If the production decryption key or encrypted secrets are committed, treat this as a security
incident:

1. Revoke/rotate the compromised key immediately.
2. Generate a fresh key and re-encrypt any production secrets.
3. Replace any credentials (API keys, passwords) that may have been exposed.
4. Remove the sensitive files from the repository history (use `git filter-repo` or `git filter-branch` or `BFG Repo-Cleaner`).
5. Notify your security contact / stakeholders.

Best practices

- Keep `config/secrets/` out of version control (it is added to `.gitignore`).
- Use your CI/CD secrets management to inject production secrets at deploy time (for example: environment variables, CI secrets, HashiCorp Vault, or cloud provider secret stores).
- Store only non-sensitive configuration in the repository and use `.env`/.env.local` for environment-specific overrides that are safe for the repo.
- Document secret rotation procedures in your ops runbook.

Local development

- For development you can create local secrets and keep the decryption key locally in `config/secrets/dev/` or use `.env.local`.
- Do not reuse production credentials in development.
