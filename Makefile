
all: install

install: composer.phar config.php
	./composer.phar install

composer.phar:
	wget https://getcomposer.org/composer.phar
	chmod +x composer.phar

config.php: config.php.dist
	cp -i $< $@

clean:
	rm -rf composer.phar vendor/

.PHONY: install clean
