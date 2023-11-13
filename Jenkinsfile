pipeline {
    agent any
 stages {
     

      stage('Get Repo') {
            steps {
                sh 'sudo rm -r /var/jenkins_home/workspace/CICDWORKFLOW/*'
                git branch: 'main', url: 'https://github.com/YourAWS-Bilal/cicd'           
            }
        }     
    stage('old build down') {
            steps {
               sh 'sudo chmod 777 -R /var/run/docker.sock'
               sh 'sudo chmod 777 -R /var/jenkins_home/workspace/CICDWORKFLOW/'
               sh 'cd /var/jenkins_home/workspace/CICDWORKFLOW/docker && docker compose down'
            }
        }
   

        
    stage('Build UP') {
            steps {
               sh 'cd /var/jenkins_home/workspace/CICDWORKFLOW/docker && docker compose up -d --build' 
            }
        }
    stage('Test') {
            steps {
               sh 'cd /var/jenkins_home/workspace/CICDWORKFLOW && phpunit --log-junit result.xml UnitTestFiles/Test/'
            }
        }

    }
}
