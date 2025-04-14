// Three.js components for interactive 3D elements
import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.132.2/build/three.module.js';
import { OrbitControls } from 'https://cdn.jsdelivr.net/npm/three@0.132.2/examples/jsm/controls/OrbitControls.js';
import { GLTFLoader } from 'https://cdn.jsdelivr.net/npm/three@0.132.2/examples/jsm/loaders/GLTFLoader.js';

class Interactive3D {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        this.controls = null;
        this.objects = [];
        this.animations = [];
    }

    init() {
        // Setup renderer
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.container.appendChild(this.renderer.domElement);

        // Setup camera
        this.camera.position.z = 5;

        // Add lights
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        this.scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
        directionalLight.position.set(1, 1, 1);
        this.scene.add(directionalLight);

        // Add orbit controls
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.05;

        // Handle window resize
        window.addEventListener('resize', () => this.onWindowResize());

        // Start animation loop
        this.animate();
    }

    onWindowResize() {
        this.camera.aspect = this.container.clientWidth / this.container.clientHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
    }

    animate() {
        requestAnimationFrame(() => this.animate());
        this.controls.update();
        this.animations.forEach(animation => animation());
        this.renderer.render(this.scene, this.camera);
    }

    addFloatingObject(geometry, material, position) {
        const mesh = new THREE.Mesh(geometry, material);
        mesh.position.set(position.x, position.y, position.z);
        this.scene.add(mesh);
        this.objects.push(mesh);

        // Add floating animation
        this.animations.push(() => {
            mesh.rotation.y += 0.01;
            mesh.position.y += Math.sin(Date.now() * 0.001) * 0.01;
        });

        return mesh;
    }

    addInteractiveObject(geometry, material, position, onClick) {
        const mesh = this.addFloatingObject(geometry, material, position);
        
        // Add click interaction
        this.renderer.domElement.addEventListener('click', (event) => {
            const raycaster = new THREE.Raycaster();
            const mouse = new THREE.Vector2();
            
            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
            
            raycaster.setFromCamera(mouse, this.camera);
            const intersects = raycaster.intersectObject(mesh);
            
            if (intersects.length > 0) {
                onClick(mesh);
            }
        });

        return mesh;
    }

    loadModel(url, position, scale) {
        const loader = new GLTFLoader();
        loader.load(url, (gltf) => {
            const model = gltf.scene;
            model.position.set(position.x, position.y, position.z);
            model.scale.set(scale.x, scale.y, scale.z);
            this.scene.add(model);
            this.objects.push(model);

            // Add animation if available
            if (gltf.animations.length > 0) {
                const mixer = new THREE.AnimationMixer(model);
                const action = mixer.clipAction(gltf.animations[0]);
                action.play();

                this.animations.push(() => {
                    mixer.update(0.016);
                });
            }
        });
    }
}

// Create interactive 3D elements for different sections
const createServiceIcons = () => {
    const service3D = new Interactive3D('service-icons-3d');
    service3D.init();

    // Add floating service icons
    const washingIcon = service3D.addInteractiveObject(
        new THREE.SphereGeometry(0.5, 32, 32),
        new THREE.MeshPhongMaterial({ color: 0x3498db }),
        { x: -2, y: 0, z: 0 },
        (mesh) => {
            mesh.material.color.setHex(0xff0000);
            setTimeout(() => mesh.material.color.setHex(0x3498db), 500);
        }
    );

    const dryCleaningIcon = service3D.addInteractiveObject(
        new THREE.BoxGeometry(0.5, 0.5, 0.5),
        new THREE.MeshPhongMaterial({ color: 0x2ecc71 }),
        { x: 0, y: 0, z: 0 },
        (mesh) => {
            mesh.rotation.x += Math.PI / 2;
        }
    );

    const ironingIcon = service3D.addInteractiveObject(
        new THREE.ConeGeometry(0.5, 1, 32),
        new THREE.MeshPhongMaterial({ color: 0xe74c3c }),
        { x: 2, y: 0, z: 0 },
        (mesh) => {
            mesh.scale.y *= 1.2;
            setTimeout(() => mesh.scale.y /= 1.2, 500);
        }
    );
};

const createPricingCards = () => {
    const pricing3D = new Interactive3D('pricing-cards-3d');
    pricing3D.init();

    // Add 3D pricing cards
    const basicCard = pricing3D.addInteractiveObject(
        new THREE.BoxGeometry(2, 3, 0.2),
        new THREE.MeshPhongMaterial({ color: 0xf1c40f }),
        { x: -3, y: 0, z: 0 },
        (mesh) => {
            mesh.rotation.y += Math.PI / 4;
        }
    );

    const premiumCard = pricing3D.addInteractiveObject(
        new THREE.BoxGeometry(2, 3, 0.2),
        new THREE.MeshPhongMaterial({ color: 0x3498db }),
        { x: 0, y: 0, z: 0 },
        (mesh) => {
            mesh.position.y += 0.5;
            setTimeout(() => mesh.position.y -= 0.5, 500);
        }
    );

    const ultimateCard = pricing3D.addInteractiveObject(
        new THREE.BoxGeometry(2, 3, 0.2),
        new THREE.MeshPhongMaterial({ color: 0xe74c3c }),
        { x: 3, y: 0, z: 0 },
        (mesh) => {
            mesh.scale.multiplyScalar(1.1);
            setTimeout(() => mesh.scale.divideScalar(1.1), 500);
        }
    );
};

// Initialize 3D components when the page loads
window.addEventListener('load', () => {
    createServiceIcons();
    createPricingCards();
}); 