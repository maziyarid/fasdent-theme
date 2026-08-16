# Reproducible Release Build

Source of truth: Git tag `v1.4.23`.

```bash
./build-release.sh
```

Build script timestamps files to the release timestamp and creates ZIPs with `zip -X`. Manifest records theme/plugin versions, hashes, target WP/PHP, commit and UTC timestamp. Deployment must verify `sha256sum -c RELEASE-MANIFEST.txt` before install.
