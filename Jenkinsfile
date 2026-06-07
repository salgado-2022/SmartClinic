pipeline {
    agent any

    stages {

        stage('1. Checkout') {
            steps {
                echo '📥 Descargando código del repositorio...'
                checkout scm
            }
        }

        stage('2. Build') {
            steps {
                echo '🐳 Construyendo imágenes Docker...'
                sh 'docker-compose build'
            }
        }

        stage('3. Test') {
            steps {
                echo '🧪 Levantando contenedores y verificando...'
                sh 'docker-compose down || true'
                sh 'docker-compose up -d'
                sh 'sleep 15'
                sh 'docker ps | grep smartclinic_app'
                sh 'docker ps | grep smartclinic_db'
                echo '✅ Contenedores de app y base de datos corriendo'
            }
        }

        stage('4. Health Check') {
            steps {
                echo '🏥 Verificando endpoint de salud...'
                sh 'sleep 10'
                sh 'curl -f http://smartclinic_app:80/health || curl -f http://localhost:8080/health'
                echo '✅ Health check exitoso - BD conectada'
            }
        }

        stage('5. Deploy') {
            steps {
                echo '🚀 Desplegando aplicación...'
                sh 'docker-compose up -d'
                echo '✅ Smart Clinic corriendo en http://localhost:8080'
            }
        }
    }

    post {
        success {
            echo '🎉 Pipeline completado con éxito - Smart Clinic desplegado'
        }
        failure {
            echo '❌ El pipeline falló, revisa los logs'
            sh 'docker-compose logs || true'
        }
    }
}
