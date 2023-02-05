#!/bin/bash -e -x

mysql -v -uroot -h127.0.0.1      < test/ddl/0010_create_database.sql
mysql -v -uroot -h127.0.0.1      < test/ddl/0020_create_user.sql
mysql -v -uroot -h127.0.0.1 test < test/ddl/0300_abc_auth_company.sql
mysql -v -uroot -h127.0.0.1 test < lib/ddl/0100_create_tables.sql

./bin/stratum stratum test/etc/stratum.ini
./bin/phpunit
