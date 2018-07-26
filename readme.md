## Test task for DOIT Software

### Task

- [x] Provide a link to the Git repo. [Here is link :)](https://github.com/VBetsun/doit-mailer)
- [ ] Upload the code to any hosting and send an URL for testing.
- [ ] Create API doc.

### Description
As a user I can:
- [x] Sign up.
- [x] Sign in.
- [ ] Enter GitHub usernames and send them a message through email.

### Requirements
- [x] Security implementation based on tokens.
- [x] A response in the JSON format.
- [x] Save avatar + thumbnail during registration.
- [ ] The same structure for all the responses.

### Installing

Cloning project:
```
git clone https://github.com/VBetsun/doit-mailer.git /path/to/project
```
Navigate to folder:
```
cd /path/to/project
```
Install packages:
```
composer install
```
Create a symbolic link from "public/storage" to "storage/app/public":
```
php artisan storage:link
```
Give www-data correct permissions for working:
```
./permissions.sh
```
Run tests
```
./vendor/bin/phpunit tests/Feature/
```
Run built-in server:
```
php artisan serve
```

Enjoy on [this url](http://127.0.0.1:8000) :)