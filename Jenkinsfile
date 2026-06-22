pipeline {
    agent any

    environment {
        COMPOSE_PROJECT = 'smartclinic'
    }

    stages {

        stage('1. Checkout') {
            steps {
                echo '📥 Descargando código del repositorio...'
                checkout scm
            }
        }

        stage('2. Build') {
            steps {
                echo '🐳 Construyendo imagen de la aplicación...'
                sh "docker-compose -p ${COMPOSE_PROJECT} build app"
            }
        }

        stage('3. Unit Tests') {
            options {
                timeout(time: 10, unit: 'MINUTES')
            }
            steps {
                echo '🧪 Ejecutando pruebas unitarias...'
                sh 'mkdir -p build/reports'
                sh 'docker rm -f smartclinic_test || true'
                sh '''docker create --name smartclinic_test -w /var/www smartclinic-app:latest sh -c "composer install && vendor/bin/phpunit --log-junit build/reports/junit.xml --coverage-text"
docker cp . smartclinic_test:/var/www
docker start -a smartclinic_test
docker cp smartclinic_test:/var/www/build/reports/junit.xml build/reports/junit.xml || true'''
            }
            post {
                always {
                    sh 'docker rm -f smartclinic_test || true'
                    junit allowEmptyResults: true, testResults: 'build/reports/junit.xml'
                }
            }
        }

        stage('4. Deploy') {
            steps {
                echo '🚀 Redesplegando app con la nueva imagen...'
                sh 'docker stop smartclinic_app || true'
                sh 'docker start smartclinic_app'
                sh 'sleep 5'
                echo '✅ App redesplegada'
            }
        }

        stage('5. Health Check') {
            steps {
                echo '🏥 Verificando endpoint de salud...'
                sh 'sleep 5'
                sh 'curl -f http://smartclinic_app:80/health || curl -f http://localhost:8080/health'
                echo '✅ Health check exitoso - App funcionando correctamente'
            }
        }
    }

    post {
        success {
            echo '🎉 Pipeline completado con éxito'
            echo '✅ App: http://localhost:8080 | Jenkins: http://localhost:9090'
        }
        failure {
            echo '❌ El pipeline falló, revisa los logs'
            sh "docker-compose -p ${COMPOSE_PROJECT} logs app db || true"
        }
    }
}
