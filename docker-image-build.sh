#!/bin/bash


AppVersion="1.0.0"
DockerHubUser="aminur93"
DockerHubRepoName="service-management-system"
DockerHubRepository="${DockerHubUser}/${DockerHubRepoName}"
#
docker login --password 2024devkinoyee@93 --username ${DockerHubUser}
###


###
BackendService="sms-backend-service"
BackendServiceDir="backend"
echo "Creating ${BackendService} Image"
docker image build --no-cache -f ${BackendServiceDir}/Dockerfile -t ${BackendService}:${AppVersion} ./${BackendServiceDir}/
docker image tag ${BackendService}:${AppVersion} ${DockerHubRepository}:${BackendService}-${AppVersion}
docker push ${DockerHubRepository}:${BackendService}-${AppVersion}
#


###
# FrontendService="sms-frontend-service"
# FrontendServiceDir="frontend"
# echo "Creating ${FrontendService} Image"
# docker image build -f ${FrontendServiceDir}/Dockerfile -t ${FrontendService}:${AppVersion} ./${FrontendServiceDir}/
# docker image tag ${FrontendService}:${AppVersion} ${DockerHubRepository}:${FrontendService}-${AppVersion}
# docker push ${DockerHubRepository}:${FrontendService}-${AppVersion}
#


###End-Of-File###
