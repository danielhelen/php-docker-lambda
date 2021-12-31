# PHP Docker Lambda

Executes PHP code within an AWS Lambda function using Docker containers.

All the heavy-lifting - such as interacting with the Lambda runtime API and proxying requests between API Gateway and
PHP itself - is handled by the base image and a package provided by [Bref](https://bref.sh/).

## Deployment

Here we use CloudFormation templates to create the AWS resources - you could also use the AWS Management Console or CLI.
Ensure you have Docker Desktop, the AWS CLI, and Composer installed.

1. Create the AWS ECR private repository using the `repository.json` CloudFormation template.

1. Create the IAM user using the `user.json` CloudFormation template and note the access key and secret access key
   available as outputs. Specify the above stack name as a parameter.

1. Add a new profile to your AWS CLI (to make things easier, keep the profile name the same as the `IMAGE_NAME` variable
   in your `.env` file):
    ```
    aws configure --profile php-docker-lambda
    ```

1. Create your `.env` file by copying the example file and filling in the details (leave `LAMBDA_FUNCTION_NAME` for
   now):
    ```
    cp .env.example .env
    ```

1. Build the Docker image and push it up to the ECR repository using the `build` bash script:
    ```
    sh ./build
    ```

1. Create the Lambda function using the `lambda.json` CloudFormation template. Specify the URI of the image in ECR as a
   parameter.

1. Update `LAMBDA_FUNCTION_NAME` variable in your `.env` file (helpful for future updates - see below).

1. Create the API Gateway using the `api-gateway.json` CloudFormation template. You will need to specify the name of the
   stack that created the Lambda function. The invocation URL is available as `InvocationURL` stack output - visiting
   that URL will invoke the Lambda function.

## Updates

When you make updates to the source code, you will need to build and push the image again:

```
sh ./build
```

The Lambda function will continue to use the previous image until you re-deploy it using the `deploy` bash script:

```
sh ./deploy
```
