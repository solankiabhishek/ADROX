// ============================================
// 3D ANIMATION with Three.js
// ============================================

let scene, camera, renderer;

function initThreeJS() {
  const container = document.getElementById('canvas-container');
  
  if (!container) return;

  // Scene setup
  scene = new THREE.Scene();
  scene.background = new THREE.Color(0x1a1a1a);

  // Camera setup
  const width = container.clientWidth;
  const height = container.clientHeight;
  camera = new THREE.PerspectiveCamera(75, width / height, 0.1, 1000);
  camera.position.z = 3;

  // Renderer setup
  renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setSize(width, height);
  renderer.setPixelRatio(window.devicePixelRatio);
  container.appendChild(renderer.domElement);

  // Create 3D objects
  const geometry = new THREE.BoxGeometry(1, 1, 1);
  const material = new THREE.MeshPhongMaterial({ 
    color: 0xE60012,
    emissive: 0x660000,
    shininess: 100
  });
  const box = new THREE.Mesh(geometry, material);
  scene.add(box);

  // Add another rotating object
  const sphereGeometry = new THREE.IcosahedronGeometry(0.7, 4);
  const sphereMaterial = new THREE.MeshPhongMaterial({
    color: 0xd4a574,
    emissive: 0x664422,
    shininess: 100
  });
  const sphere = new THREE.Mesh(sphereGeometry, sphereMaterial);
  sphere.position.x = 2;
  scene.add(sphere);

  // Lighting
  const light1 = new THREE.PointLight(0xE60012, 1, 100);
  light1.position.set(5, 5, 5);
  scene.add(light1);

  const light2 = new THREE.PointLight(0xffffff, 0.5, 100);
  light2.position.set(-5, -5, 5);
  scene.add(light2);

  const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
  scene.add(ambientLight);

  // Animation loop
  function animate() {
    requestAnimationFrame(animate);

    // Rotate objects
    box.rotation.x += 0.005;
    box.rotation.y += 0.005;
    box.position.x = Math.sin(Date.now() * 0.0003) * 0.5;

    sphere.rotation.x -= 0.003;
    sphere.rotation.y -= 0.003;
    sphere.position.y = Math.cos(Date.now() * 0.0003) * 0.5;

    renderer.render(scene, camera);
  }

  animate();

  // Handle window resize
  window.addEventListener('resize', () => {
    const newWidth = container.clientWidth;
    const newHeight = container.clientHeight;
    camera.aspect = newWidth / newHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(newWidth, newHeight);
  });
}

// Initialize on window load
window.addEventListener('load', () => {
  initThreeJS();
});
