# CHANGELOG

#### 2019-03-08

* Added support for empty passwords
* Added support for a `TRUSTEDPROXIES` variable, containing a comma separated list of trusted proxy IPs
* Added support for a `DROP_DATABASE` variable to allow the installation to be completely unattended
* Added command `sw:theme:synchronize` to be run automatically on an update


* Removed individual database environment variables `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` and `DB_PORT` in favor of the general `DATABASE_URL`.
If your password contains any special characters, be sure to URL-encode them for the URL.