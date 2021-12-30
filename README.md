# PHP Docker Lambda

Executes PHP code within an AWS Lambda function using Docker containers.

All the heavy-lifting - such as interacting with the Lambda runtime API and proxying requests between API Gateway and
PHP itself - is handled by the base image and a package provided by [Bref](https://bref.sh/).

## Deployment

Deploying a container image is a bit fiddly. Here we use CloudFormation templates to create the AWS resources - you
could also use the AWS Management Console or CLI. Ensure you have Docker Desktop and the AWS CLI, and Composer
installed.

1. Install the composer packages:
    ```
    composer install -d ./src
    ```

2. Create the AWS ECR private repository using the `repository.json` CloudFormation template.

3. Create the IAM user using the `user.json` CloudFormation template and note the access key and secret access key
   available as outputs. Specify the above stack name as a parameter.

4. Add a new profile to your AWS CLI:
    ```
    aws configure --profile php-docker-lambda
    ```

5. Build the image:
    ```
    docker build -t php-docker-lambda .
    ```

6. Tag the image (specify your AWS account number and region):
    ```
    docker tag php-docker-lambda:latest AWS_ACCOUNT_ID.dkr.ecr.AWS_REGION.amazonaws.com/php-docker-lambda:latest
    ```

7. Before you can push that tag you need to authenticate the Docker CLI to the Amazon ECR private repository (specify
   the AWS account number and region again):
    ```
    aws ecr get-login-password --profile php-docker-lambda | docker login --username AWS --password-stdin AWS_ACCOUNT_ID.dkr.ecr.AWS_REGION.amazonaws.com
    ```

8. Push the tagged image to the ECR repository (again specify your AWS account number and region):
    ```
    docker push AWS_ACCOUNT_ID.dkr.ecr.AWS_REGION.amazonaws.com/php-docker-lambda:latest
    ```

9. Create the Lambda function using the `lambda.json` CloudFormation template. Specify the URI of the image in ECR as a
   parameter.

10. Create the API Gateway using the `api-gateway.json` CloudFormation template. You will need to specify the name of
    the stack that created the Lambda function. The invocation URL is available as `InvocationURL` stack output -
    visiting that URL will invoke the Lambda function.

## Updates

When you make updates to the source code, you will need to re-build and push the image.

When you push up a new image to the registry, the Lambda function remains unchanged.

Run the following to deploy the new image:

```
aws lambda update-function-code --profile php-docker-lambda --function-name LAMBDA_FUNCTION_NAME --image-uri AWS_ACCOUNT_ID.dkr.ecr.AWS_REGION.amazonaws.com/php-docker-lambda:latest
```
