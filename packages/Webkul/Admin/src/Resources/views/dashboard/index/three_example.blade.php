<div id="three-wrapper">
    <div class="three-container" style="width: 100%; height: 500px;"></div>
</div>

<script type="module">
    import * as THREE from 'https://unpkg.com/three@0.126.1/build/three.module.js';

    import { OrbitControls } from 'https://unpkg.com/three@0.126.1/examples/jsm/controls/OrbitControls.js';

    const container = document.querySelector('#three-wrapper .three-container');

    if (container) {
        // Crear la escena, cámara y renderizador
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({
            alpha: true, // Fondo transparente
            antialias: true, // Mejora la calidad visual
        });
        renderer.setSize(container.clientWidth, container.clientHeight);
        container.appendChild(renderer.domElement);

        // Configurar la luz
        const light = new THREE.PointLight(0xffffff, 1, 100);
        light.position.set(10, 10, 10);
        scene.add(light);

        // Crear el cubo
        const geometry = new THREE.BoxGeometry();
        const material = new THREE.MeshStandardMaterial({ color: 0xffffff });
        const cube = new THREE.Mesh(geometry, material);
        scene.add(cube);

        // Configurar la posición de la cámara
        camera.position.z = 5;

        // Controles de órbita opcionales
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;

        // Animación
        const animate = () => {
            cube.rotation.x += 0.01;
            cube.rotation.y += 0.01;

            controls.update(); // Actualizar controles
            renderer.render(scene, camera);
            requestAnimationFrame(animate);
        };

        // Listener para evitar liberación de recursos
        let animationFrameId;
        const startAnimation = () => {
            if (!animationFrameId) {
                animationFrameId = requestAnimationFrame(animate);
            }
        };

        const stopAnimation = () => {
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
                animationFrameId = null;
            }
        };

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                startAnimation();
            } else {
                stopAnimation();
            }
        });

        // Iniciar animación
        startAnimation();

        // Limpieza de recursos al cerrar
        window.addEventListener('beforeunload', () => {
            stopAnimation();
            renderer.dispose();
            controls.dispose();
        });

        // Ajuste de tamaño en cambio de ventana
        window.addEventListener('resize', () => {
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        });
    } else {
        console.error('Contenedor no encontrado.');
    }
</script>
