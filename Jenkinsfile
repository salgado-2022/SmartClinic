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

        stage('2. Cleanup') {
            steps {
                echo '🧹 Limpiando contenedores previos (sin tocar Jenkins)...'
                sh 'docker rm -f smartclinic_app smartclinic_db smartclinic_test || true'
            }
        }

        stage('3. Build') {
            steps {
                echo '🐳 Construyendo imagen de la aplicación...'
                sh "docker-compose -p ${COMPOSE_PROJECT} build app"
            }
        }

        stage('4. Unit Tests') {
            options {
                timeout(time: 10, unit: 'MINUTES')
            }
            steps {
                echo '🧪 Ejecutando pruebas unitarias...'
                sh 'mkdir -p build/reports'
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

        stage('5. Integration Test') {
            steps {
                echo '🧪 Levantando app y base de datos...'
                sh "docker-compose -p ${COMPOSE_PROJECT} up -d db"
                sh 'sleep 15'
                sh "docker-compose -p ${COMPOSE_PROJECT} up -d app"
                sh 'sleep 10'
                sh 'docker ps | grep smartclinic_app'
                sh 'docker ps | grep smartclinic_db'
                echo '✅ Contenedores de app y base de datos corriendo'
            }
        }

        stage('6. Health Check') {
            steps {
                echo '🏥 Verificando endpoint de salud...'
                sh 'sleep 5'
                sh 'curl -f http://smartclinic_app:80/health || curl -f http://localhost:8080/health'
                echo '✅ Health check exitoso - BD conectada'
            }
        }

        stage('7. Deploy') {
            steps {
                echo '🚀 Aplicación desplegada correctamente'
                echo '✅ Smart Clinic corriendo en http://localhost:8080'
            }
        }
    }

    post {
        success {
            echo '🎉 Pipeline completado con éxito - Smart Clinic desplegado'
            echo '✅ App: http://localhost:8080 | Jenkins: http://localhost:9090'
        }
        failure {
            echo '❌ El pipeline falló, revisa los logs'
            sh "docker-compose -p ${COMPOSE_PROJECT} logs app db || true"
            sh 'docker rm -f smartclinic_app smartclinic_db || true'
        }
    }
}
