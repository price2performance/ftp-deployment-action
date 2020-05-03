# FTP Deployment Action

**CURRENTLY IN DEVELOPMENT**. Use at your own risk! Or rather wait for a final release.

GitHub Action allows using a great [dg/ftp-deployment](https://github.com/dg/ftp-deployment) tool for FTP deployment
as a part of your Workflow.

This Action takes the working directory from your Workflow and makes automatic deployment to FTP/SFTP/FTPS using
configuration in specified `deployment.ini` file. For full documentation of configuration, see [dg/ftp-deployment](https://github.com/dg/ftp-deployment). 

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

You do not have to put your secret password in `deployment.ini` file. Just replace your sensitive data in keys:
`remote`, `user` or `password` with a placeholder of any valid name key name in PHP.

```ini
remote = sftp://{{SECRET_USER}}:{{SECRET_PASSWORD}}@{{SECRET_HOST}}/{{SECRET_DIR}}
```

Then pass the environment variable in your workflow yaml file:

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

Context `secrets` in your `yml` file is a standard feature of GitHub Actions. For more information see the [official documentation](https://help.github.com/en/actions/configuring-and-managing-workflows/creating-and-storing-encrypted-secrets).