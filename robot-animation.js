import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.132.2/build/three.module.js';
import { GLTFLoader } from 'https://cdn.jsdelivr.net/npm/three@0.132.2/examples/jsm/loaders/GLTFLoader.js';

class RobotAnimation {
    constructor() {
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        this.robot = null;
        this.mixer = null;
        this.clock = new THREE.Clock();
        this.position = { x: -10, y: 0, z: 0 };
        this.targetPosition = { x: 10, y: 0, z: 0 };
        this.speed = 0.05;
        this.bounceHeight = 0.5;
        this.bounceSpeed = 0.1;
        this.rotationSpeed = 0.02;
    }

    init() {
        // Setup renderer
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.renderer.setClearColor(0x000000, 0);
        document.getElementById('robot-container').appendChild(this.renderer.domElement);

        // Setup camera
        this.camera.position.set(0, 2, 5);
        this.camera.lookAt(0, 0, 0);

        // Add lights
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        this.scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
        directionalLight.position.set(1, 1, 1);
        this.scene.add(directionalLight);

        // Add a simple floor
        const floorGeometry = new THREE.PlaneGeometry(20, 2);
        const floorMaterial = new THREE.MeshBasicMaterial({ 
            color: 0xffffff,
            transparent: true,
            opacity: 0.1
        });
        const floor = new THREE.Mesh(floorGeometry, floorMaterial);
        floor.rotation.x = -Math.PI / 2;
        floor.position.y = -1;
        this.scene.add(floor);

        // Load robot model
        const loader = new GLTFLoader();
        loader.load('models/robot.glb', (gltf) => {
            this.robot = gltf.scene;
            this.robot.scale.set(0.3, 0.3, 0.3);
            this.robot.position.set(this.position.x, this.position.y, this.position.z);
            this.scene.add(this.robot);

            // Add toy-like materials
            this.robot.traverse((child) => {
                if (child.isMesh) {
                    child.material = new THREE.MeshPhongMaterial({
                        color: 0xFFD700,
                        shininess: 100,
                        specular: 0x111111
                    });
                }
            });

            // Setup animation mixer
            this.mixer = new THREE.AnimationMixer(this.robot);
            const clip = gltf.animations[0];
            const action = this.mixer.clipAction(clip);
            action.play();
        });

        // Handle window resize
        window.addEventListener('resize', () => this.onWindowResize());

        // Add mouse interaction
        document.addEventListener('mousemove', (event) => {
            if (this.robot) {
                const mouseX = (event.clientX / window.innerWidth) * 2 - 1;
                this.robot.rotation.y = mouseX * 0.5;
            }
        });

        // Start animation loop
        this.animate();
    }

    onWindowResize() {
        this.camera.aspect = window.innerWidth / window.innerHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(window.innerWidth, window.innerHeight);
    }

    animate() {
        requestAnimationFrame(() => this.animate());

        // Update robot position
        if (this.robot) {
            // Move robot
            this.position.x += this.speed;
            if (this.position.x > this.targetPosition.x) {
                this.position.x = -10;
            }

            // Add bouncing effect
            const bounce = Math.sin(this.position.x * this.bounceSpeed) * this.bounceHeight;
            this.robot.position.set(
                this.position.x,
                this.position.y + bounce,
                this.position.z
            );

            // Add playful rotation
            this.robot.rotation.y += this.rotationSpeed;
            this.robot.rotation.x = Math.sin(this.position.x * 0.2) * 0.1;
        }

        // Update animation mixer
        if (this.mixer) {
            this.mixer.update(this.clock.getDelta());
        }

        this.renderer.render(this.scene, this.camera);
    }
}

// Initialize robot animation
const robotAnimation = new RobotAnimation();
robotAnimation.init(); 