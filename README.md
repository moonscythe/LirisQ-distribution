# LirisQ distribution

This repository contains published LirisQ installation artifacts and update manifests only. It never contains source code, private configuration, credentials, user data, or database exports.

- `main` is the public-release channel.
- `development` is the development/test channel.

## Release layout

- `releases/<version>/` contains immutable PHP installer, Node installer, and
  public updater archives with SHA-256 checksum files.
- `latest/` contains the current PHP and Node installer aliases plus the public
  updater manifest and archive used by in-app public update checks.

This repository intentionally does not include source code, internal plans,
blueprints, tests, or deployment credentials.
