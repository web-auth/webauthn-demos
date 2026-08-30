# WebAuthn Demos

Runnable demonstrations of [web-auth/webauthn-framework](https://github.com/web-auth/webauthn-framework), the FIDO2 / WebAuthn framework for PHP and Symfony.

Each directory is a self-contained project with its own `composer.json` and its own README.

## Pure PHP demos

They use `web-auth/webauthn-lib` only: no Symfony bundle, no Doctrine, no database. Every one of them boots with a single command and stores its state in a local file.

| Demo | What it shows |
| --- | --- |
| [basic-demo](basic-demo) | Username-based passkey registration, sign in and credential management |
| [usernameless-demo](usernameless-demo) | Sign in without a username, with discoverable credentials and Conditional UI |
| [passkey-upgrade-demo](passkey-upgrade-demo) | Silent passkey enrollment after a password login, with Conditional Create |
| [signal-api-demo](signal-api-demo) | The three W3C Signal methods that keep the authenticator in sync with the server |
| [extensions-demo](extensions-demo) | `credProps`, `credProtect`, `credBlob` and `minPinLength` in a single ceremony |
| [prf-demo](prf-demo) | The PRF extension used for client-side encryption, including an offline vault |
| [spc-demo](spc-demo) | Secure Payment Confirmation, in a same-origin, two-origin and three-tier flavour |

### Running one of them

```bash
cd basic-demo
./same-origin/run.sh
```

The script installs the dependencies on first run and serves the demo on <http://localhost:8000>. Requirements: PHP 8.2 or higher with `ext-openssl`, Composer, and a browser with passkey support.

These demos require `web-auth/webauthn-framework` in its `5.4.x-dev` version, as they exercise features that are not released yet.

## Symfony demo application

[symfony-demo](symfony-demo) is a complete Symfony application using the WebAuthn bundle, FrankenPHP, AssetMapper and Tailwind. It is built and run with Docker, see its own README.

> [!NOTE]
> The application still targets `web-auth/* ^4.1` and Symfony 6.4. Bringing it to the 5.x line is a separate piece of work.

## Documentation

The full documentation lives at <https://webauthn-doc.spomky-labs.com/>.

## License

MIT, see [LICENSE](LICENSE).
