# Superkawaiicrew Template - Dokuwiki

## Development

### only the theme

So you only want to create theme. Fair enough.
The docker-compose.yml file is already configured so you can start
developing on the skc theme.

```bash
$ docker-compose up -d
```

Wait a little bit and then fire up your browser and type in **localhost**.
Login in with username `superkawaii` and password `superkawaii`.  
Go to Admin - Configuration Manager - template (forth option) and set
it to your template name.

Et Voila! It works.

You also need to edit our smartass .gitignore file.
Just add your directory `!tpl/YOURNAME` and git tracks your stuff :)