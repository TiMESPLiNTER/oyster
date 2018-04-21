# Oyster 🐚

Oyster is the shell of all shells. Written in PHP.

To get started with it just run `composer install` and set the `console` file as your default shell and magic will happen.

If you don't trust Oyster yet: just execute the `./console` file from your current shell and you will see that the world
can look way brighter and better listening and talking to the right shell.

## Configuration

Of course you can adjust Oyster so it fits your needs and style. Oyster will look for a `.oysterrc` file in your home
directory.

````json
{
    "ps1": "{%CURRENT_DIRECTORY%}$ ",
    "env": {
        "vars": {
            "PATH": "/usr/local/bin:/usr/local/sbin:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:/usr/local/MacGPG2/bin:/Users/Pascal/bin"
        }
    }
}
````