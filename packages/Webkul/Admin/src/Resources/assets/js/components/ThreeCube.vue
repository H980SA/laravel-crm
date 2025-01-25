<template>
    <div class="three-container" style="width: 100%; height: 500px; border: 1px solid #ddd;"></div>
</template>

<script type="module">
import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.152.2/build/three.module.js';

export default {
    name: 'ThreeCube',
    mounted() {
        this.initializeThreeJS();
    },
    methods: {
        initializeThreeJS() {
            const container = this.$el.querySelector('.three-container');

            if (container) {
                // Crear la escena, cámara y renderizador
                const scene = new THREE.Scene();
                const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
                const renderer = new THREE.WebGLRenderer({
                    alpha: true,
                    antialias: true,
                });

                renderer.setSize(container.clientWidth, container.clientHeight);
                container.appendChild(renderer.domElement);

                // Luz
                const light = new THREE.PointLight(0xffffff, 1, 100);
                light.position.set(10, 10, 10);
                scene.add(light);

                // Cubo blanco
                const geometry = new THREE.BoxGeometry();
                const material = new THREE.MeshStandardMaterial({ color: 0xffffff });
                const cube = new THREE.Mesh(geometry, material);
                scene.add(cube);

                camera.position.z = 5;

                // Animar el cubo
                const animate = () => {
                    cube.rotation.x += 0.01;
                    cube.rotation.y += 0.01;
                    renderer.render(scene, camera);
                    requestAnimationFrame(animate);
                };

                // Ajustar tamaño en redimensionamiento
                window.addEventListener('resize', () => {
                    camera.aspect = container.clientWidth / container.clientHeight;
                    camera.updateProjectionMatrix();
                    renderer.setSize(container.clientWidth, container.clientHeight);
                });

                animate();
            } else {
                console.error('Contenedor no encontrado.');
            }
        },
    },
};
</script>
