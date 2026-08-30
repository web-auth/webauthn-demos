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

The script installs the dependencies on first run and serves the demo on <http://localhost:8000>. Set `PORT` to serve it elsewhere. Requirements: PHP 8.2 or higher with `ext-openssl`, Composer, and a browser with passkey support.

These demos require `web-auth/webauthn-framework` in its `5.4.x-dev` version, as they exercise features that are not released yet.

### Running all of them at once

```bash
./run-all.sh
```

Every demo gets its own port, so they can be compared side by side. They all share the `localhost` Relying Party ID, which means a passkey registered in one of them is visible to the others.

| URL | Demo |
| --- | --- |
| <http://localhost:8101> | basic-demo |
| <http://localhost:8102> | usernameless-demo |
| <http://localhost:8103> | passkey-upgrade-demo |
| <http://localhost:8104> | signal-api-demo |
| <http://localhost:8105> | extensions-demo |
| <http://localhost:8106> | prf-demo |
| <http://localhost:8107> | spc-demo (same-origin flavour) |
| <http://localhost:8110> | symfony-demo |

Ctrl+C stops everything. The two other flavours of the SPC demo need several origins at once and keep their own launchers, see [spc-demo](spc-demo).

## Symfony demo application

[symfony-demo](symfony-demo) is a complete Symfony application using the WebAuthn bundle, FrankenPHP, AssetMapper and Tailwind. It targets Symfony 8.1, `web-auth/* ^5.3` and PHP 8.4, and is built and run with Docker, see its own README.

`run-all.sh` points it at a SQLite database created on first run and serves it over plain HTTP, which `localhost` makes a secure context. The origin it accepts comes from `WEBAUTHN_ALLOWED_ORIGIN`, `http://localhost:8110` by default, so change it in `symfony-demo/.env.local` if you serve the application elsewhere. Its own Docker setup keeps PostgreSQL and HTTPS: set `DATABASE_URL` in `symfony-demo/.env.local` and run `php bin/console doctrine:migrations:migrate` to work against PostgreSQL instead.

> [!NOTE]
> FrankenPHP worker mode is disabled for now: `runtime/frankenphp-symfony` does not support Symfony 8 yet. The application runs in the classic mode until it does.

## Documentation

The full documentation lives at <https://webauthn-doc.spomky-labs.com/>.

## License

MIT, see [LICENSE](LICENSE).
