# FTP Deployment Action

**CURRENTLY IN DEVELOPMENT**. Use at your own risk! Or rather wait for a final release. Help with testing and feedback is welcome.

GitHub Action allows using a great [dg/ftp-deployment](https://github.com/dg/ftp-deployment) tool for FTP deployment
as a part of your Workflow.

This Action takes the working directory from your Workflow and makes automatic deployment to FTP/SFTP/FTPS using
configuration in specified `deployment.ini` file. For full documentation of configuration, see [dg/ftp-deployment](https://github.com/dg/ftp-deployment).

To see this Action in action, check the example at [price2performace/ftp-deployment-example](https://github.com/price2performance/ftp-deployment-example).  

## Inputs

### `config_file`

**Required** INI file with deployment configuration. Default `deployment.ini`.

### `parameters`

(optional) Parameters to run the deployment with.

## Example usage

Use in your workflow `yml` file like this:

```yaml
uses: price2performance/ftp-deployment-action@master
with:
  config_file: ./.deployment/config.ini
  parameters: --test --section name
``` 

## Secrets

You do not have to put your secret password in `deployment.ini` file. You can use GitHub Secrets instead
(for more information about GitHub Secrets, see the [official documentation](https://help.github.com/en/actions/configuring-and-managing-workflows/creating-and-storing-encrypted-secrets).)

Just replace your sensitive data in `deployment.ini` with a placeholder of any valid array key name in PHP:

```ini
remote = sftp://{{SECRET_USER}}:{{SECRET_PASSWORD}}@{{SECRET_HOST}}/{{SECRET_DIR}}
```

Then pass the environment variables with same key in your workflow yaml file:

```yaml
uses: price2performance/ftp-deployment-action@master
with:
  config_file: ./.deployment/config.ini
  parameters: --test --section name
  env:
    SECRET_USER: ${{ secrets.sftp_user }}
    SECRET_PASSWORD: ${{ secrets.sftp_password }}
    SECRET_HOST: ${{ secrets.sftp_host }}
    SECRET_DIR: ${{ secrets.sftp_initial_dir }}
```

This Action then takes your deployment.ini script and replaces all the placeholders on-fly with the passed secrets.
Please kindly note, that this replacement is limited only to keys: `remote`, `user` and `password` in your configuration file (in any section).   

Context `secrets` in your `yml` file is a standard feature of GitHub Actions. 

## Docker container

The action can be used as a standalone docker container. To do this, you must mount the root of your deployed application
into the container's workdir. You can pass configuration file and other parameters on the end of the docker run command:

```bash
docker run --rm -it -w=/app -v "D:\www\ftp-deployment-example:/app" price2performance/ftp-deployment:latest deployment.ini --test
```

If your deployment.ini contains secret placeholders (e.g. `{{SECRET_HOST}}`) and you want to use it, you must pass all the inputs as environment
variables:

```bash
docker run --rm -it -w=/app -v "D:\www\ftp-deployment-example:/app" -e INPUT_CONFIG_FILE=deployment.ini -e INPUT_PARAMETERS=--test -e SECRET_HOST=host123.example.com -e SECRET_USER=exampleuser001 -e SECRET_PASSWORD=password123 -e SECRET_DIR=initial_dir/www price2performance/ftp-deployment:latest
```