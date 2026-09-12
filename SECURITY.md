# Security Policy

## Reporting a Vulnerability

Please report security issues to **[security@b13.com](mailto:security@b13.com)**,
not through the public issue tracker.

Helpful in a report:

- The extension version and the TYPO3 version
- What an attacker can do, and what access they need to do it
- Steps to reproduce, or a proof of concept
- Whether the issue is already public anywhere

If you would like to encrypt your report, ask us for a key first.

## What to Expect

We read every report and reply to it. You will hear back from us with an
assessment of the finding, and we agree a disclosure date with you before
anything is published.

We do not publish fixed response times. We would rather answer you quickly than
name a deadline we might miss—if a date matters for your own disclosure
process, say so in your report and we will agree one with you.

We publish a fix before we describe the issue. Advisories appear next to the
[changelog](CHANGELOG.md) once a fixed version is available, and we credit
reporters who want to be credited.

## Supported Versions

Security fixes go into the current minor release of each supported major
version. There are no backports to majors that have reached their end of
support.

The extension's own support window follows the TYPO3 versions it declares in
`composer.json`. See the [changelog](CHANGELOG.md) for the version history.

## Data This Extension Handles

Worth knowing when you assess the impact of a finding:

- **The extension holds no data of its own.** It has no database tables, no
  configuration of its own, and writes nothing. It renders content that is
  already in `tt_content`.
- **It renders in the backend only**, inside the page module. Nothing it
  produces is reachable from the frontend.
- **Preview templates decide what is shown.** The templates shipped here render
  fields of the content element that the editor is already allowed to see in
  that module. A template added in a project can widen that—the
  `b13:getDatabaseRecord` ViewHelper reads any table it is pointed at, without
  applying backend user permissions to the result. Treat a preview template as
  code, and do not use it to surface records an editor may not see otherwise.
- **The edit link is permission-checked.** It is only assigned when the backend
  user may edit the record.

## Scope

In scope: the code in this repository.

Out of scope: vulnerabilities in TYPO3 core (report those to the
[TYPO3 Security Team](https://typo3.org/help/security-advisories)) or in a
third-party extension.
