
Hello, world!
=============

My name is Dmitry and there is some creepy code for Mikhail S.


Technologies used
-----------------

 * Vagrant for running clean isolated environment
 * Ansible for configuring everything
 * Composer to manage PHP packages
 * Silex, powerful microframework, with some additional components:
    - Doctrine DBAL
    - Symfony Forms component
    - Twig templating library
 * Last but not least, Twitter Bootstrap for basic CSS


Requirements
------------

Tested on Debian Sid with Vagrant 1.6, should work on recent Ubuntu versions.


Getting started
---------------

 1. Install Vagrant, if not installed yet
 2. Run `vagrant up` in project folder
 3. Open http://127.0.0.1:8080/ in your browser


Troubleshoting
--------------

If port 8080 is not available, you can edit `Vagrantfile` and change it.


How to test it
--------------

By default, password is handled as not hashed (and salt is ignored).
You may log in with such credentials:

| url                                 | email             | password                           |
| ----------------------------------- | ----------------- | ---------------------------------- |
| [/admin/login][1]                   | `jim@testnet.com` | `mssZSE4Q3OBhg`                    |
| [/sales/login][2]                   | `sales1@mail.com` | `88312213c3492c4cd89d297f16cb0fc4` |
| [/site/login/pid/RVM1G5621DGYHI][3] | `as@a.com`        | `68d110142f82cadab74676cc97ff6317` |

[1]: http://127.0.0.1:8080/admin/login
[2]: http://127.0.0.1:8080/sales/login
[3]: http://127.0.0.1:8080/site/login/pid/RVM1G5621DGYHI


How to use real secure hashing
------------------------------

 1. Uncomment first line in `LoginService::checkPassword`.
 2. Run `vagrant ssh` in project folder, then run `mysql` and paste this code:

```sql
UPDATE `as_users` SET pwd =
'$2y$10$88xKYwareQRrtvV5ng8kWerrsoCvkFho9Y1HgRjn65BSqr6kMCDYG';

UPDATE `users` SET user_password =
'$2y$10$88xKYwareQRrtvV5ng8kWerrsoCvkFho9Y1HgRjn65BSqr6kMCDYG';

/* password_hash('test01', PASSWORD_DEFAULT); */
```

Now you can log in with same emails and with password `test01`.
