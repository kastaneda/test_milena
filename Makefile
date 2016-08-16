
all: install

install: composer.phar
	./composer.phar install

composer.phar:
	wget https://getcomposer.org/composer.phar
	chmod +x composer.phar

clean:
	rm -rf composer.phar vendor/

.PHONY: install clean
