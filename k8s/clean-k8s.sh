#!/bin/bash
# File name: clean-k8s.sh
# Purpose: Delete all Kubernetes resources (pods, services, ingress, hpa, deployments, replicasets)

echo "Deleting all pods..."
kubectl delete pods --all

echo "Deleting all services..."
kubectl delete svc --all

echo "Deleting all ingress..."
kubectl delete ingress --all

echo "Deleting all horizontal pod autoscalers..."
kubectl delete hpa --all

echo "Deleting all deployments and replicasets..."
kubectl delete deploy --all
kubectl delete rs --all

echo "All Kubernetes resources have been cleaned!"