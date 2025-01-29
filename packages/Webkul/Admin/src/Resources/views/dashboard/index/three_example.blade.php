@php
    $businessLine = $businessLine ?? 'IM';
@endphp

<div id="three-wrapper-{{ $businessLine }}" style="position: relative;">
    <div id="pipelineTitle-{{ $businessLine }}" style="
      position: absolute;
      top: 10px; left: 50%;
      transform: translateX(-50%);
      font-size: 24px;
      font-weight: bold;
      color: #fff;
      z-index: 10;
      pointer-events: none;
    ">
      Pipeline {{ $businessLine }}
    </div>
  
    <div id="three-container-{{ $businessLine }}" class="three-container" style="
    width: 100%;
    height: 500px;
    position: relative;
    border: none;
    background: none; 
    "></div>
  </div>
  
  <script type="module">
    import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.152.2/build/three.module.js';
  
    const businessLine = "{{ $businessLine }}";    
    console.log("🔹 Business Line recibida:", businessLine);
    const savedFiltersUrl = "{{ route('admin.datagrid.saved_filters.index') }}";
    console.log(savedFiltersUrl);
    let leadsData = [
      { id: null, label: '0. Búsqueda Oportunidades (Prospección)' },
      { id: null, label: '1. Recepción de invitación y evaluar participación' },
      { id: null, label: '2. Visita de campo y contactos' },
      { id: null, label: '3. Consultas & Respuestas' },
      { id: null, label: '4. Preparación, Revisión interna, Presentación' },
      { id: null, label: '5. Ajuste, Revisión final (Cierre)' },
      { id: null, label: '6. Revisión y Aprendizaje Post-Licitación' },
    ];

    async function fetchFilters() {
        try {
            const src = "http://localhost:8000/admin/leads";  // Asegúrate de que este valor sea correcto
            console.log("🔍 Enviando src:", src);

            const response = await fetch(`${savedFiltersUrl}?src=${encodeURIComponent(src)}`);
            const data = await response.json();

            console.log("✅ Respuesta recibida:", data);

            if (!data.data || !Array.isArray(data.data)) {
                console.error("⚠️ Error: La API no devolvió datos válidos", data);
                return;
            }

            console.log("📊 Filtros recibidos:", data.data);

            // Filtrar solo los filtros que coincidan con la línea de negocio (M, ST, IM)
            const filters = data.data.filter(filter => filter.name.startsWith(businessLine + "-"));
            console.log(`📌 Filtros de la línea de negocio (${businessLine}):`, filters);

            // Mapear nombres de etapas a IDs de filtros
            const stageNames = [
                "Búsqueda-Oportunidades",
                "RecepciónInvitación",
                "Visita-Campo-Contactos",
                "Consultas-Respuestas",
                "Preparacion-RevisionInterna-Presentacion",
                "Ajuste-RevisionFinal",
                "Post-Licitación"
            ];

            stageNames.forEach((stageName, index) => {
                const filter = filters.find(f => f.name.includes(stageName));
                if (filter) {
                    leadsData[index].id = filter.id;  // Asigna el ID del filtro al leadsData
                }
            });

            console.log("✅ Leads Data actualizado:", leadsData);

        } catch (error) {
            console.error("❌ Error al obtener los filtros:", error);
        }
    }

    fetchFilters();
  
    setTimeout(() => {
      const container = document.getElementById('three-container-{{ $businessLine }}');
      if (!container) {
        console.error('No se encontró el contenedor para', "{{ $businessLine }}");
        return;
      }
  
      
      const scene = new THREE.Scene();
      const camera = new THREE.PerspectiveCamera(
        75,
        container.clientWidth / container.clientHeight,
        0.1,
        1000
      );
      camera.position.z = 15;
      camera.position.y = 5;
      camera.rotation.x = -0.3;
  
      
      const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
      renderer.setSize(container.clientWidth, container.clientHeight);
      renderer.setClearColor(0x000000, 0); 
      container.appendChild(renderer.domElement);
  
      
      const raycaster = new THREE.Raycaster();
      const mouse = new THREE.Vector2();
  
      
      const pointLight = new THREE.PointLight(0xffffff, 1, 100);
      pointLight.position.set(10, 10, 10);
      scene.add(pointLight);
  
      const ambientLight = new THREE.AmbientLight(0x404040);
      scene.add(ambientLight);
  
      
      const colors = [
        0x00ff00, 0x0099ff, 0x66ccff,
        0xffff00, 0xff9900, 0xff0000,
        0x800080
      ];
      const startRadius = 5;
      const endRadius   = 0.5;
      const totalLength = 20;
      const numSections = colors.length;
      const gap         = 0.2;
      const totalGap    = gap * (numSections - 1);
      const effectiveLength = totalLength - totalGap;
      const sectionLength   = effectiveLength / numSections;
  
      
      function lightenColor(originalHex, factor = 1.8) {
        const color = new THREE.Color(originalHex);
        color.multiplyScalar(factor);
        return color.getHex();
      }
  
      
      const sections = [];
      for (let i = 0; i < numSections; i++) {
        const r1 = startRadius - ((startRadius - endRadius) / numSections) * i;
        const r2 = startRadius - ((startRadius - endRadius) / numSections) * (i + 1);
  
        const geometry = new THREE.CylinderGeometry(r2, r1, sectionLength, 32, 1, false);
        const material = new THREE.MeshStandardMaterial({ color: colors[i], flatShading: true });
        const section = new THREE.Mesh(geometry, material);
  
        
        section.userData = {
          leadId: leadsData[i].id,        
          label: leadsData[i].label,      
          originalColor: colors[i],
          hovered: false,
          basePosition: new THREE.Vector3(),
          currentOffsetY: 0,
          targetOffsetY: 0,
          offsetHoverY: 2,
          currentScale: 1,
          targetScale: 1,
          scaleHover: 1.1,
        };
  
        
        const posX = - (totalLength / 2) + i * (sectionLength + gap) + sectionLength / 2;
        section.position.set(posX, 0, 0);
        section.rotation.z = -Math.PI / 2;
        section.userData.basePosition.copy(section.position);
  
        scene.add(section);
        sections.push(section);
      }
  
      
      const overlays = sections.map((section, idx) => {
        const overlayDiv = document.createElement('div');
        overlayDiv.style.position = 'absolute';
        overlayDiv.style.top = '0';
        overlayDiv.style.left = '0';
        overlayDiv.style.width = '0';
        overlayDiv.style.height = '0';
        overlayDiv.style.pointerEvents = 'none'; 
        overlayDiv.style.display = 'none';       
  
        
        const svgNS = 'http://www.w3.org/2000/svg';
        const arrowLine = document.createElementNS(svgNS, 'svg');
        arrowLine.setAttribute('width', '200');
        arrowLine.setAttribute('height', '200');
        arrowLine.style.overflow = 'visible';
  
        
        const line = document.createElementNS(svgNS, 'line');
        line.setAttribute('x1', '0');
        line.setAttribute('y1', '0');
        line.setAttribute('x2', '100');
        line.setAttribute('y2', '0');
        line.setAttribute('stroke', 'blue');
        line.setAttribute('stroke-width', '2');
  
        
        const defs = document.createElementNS(svgNS, 'defs');
        const marker = document.createElementNS(svgNS, 'marker');
        marker.setAttribute('id', `arrow-marker-${idx}`);
        marker.setAttribute('markerWidth', '10');
        marker.setAttribute('markerHeight', '10');
        marker.setAttribute('refX', '5');
        marker.setAttribute('refY', '2');
        marker.setAttribute('orient', 'auto');
        marker.setAttribute('markerUnits', 'strokeWidth');
  
        const arrowPath = document.createElementNS(svgNS, 'path');
        arrowPath.setAttribute('d', 'M0,0 L0,4 L4,2 z');
        arrowPath.setAttribute('fill', 'blue');
        marker.appendChild(arrowPath);
        defs.appendChild(marker);
        arrowLine.appendChild(defs);
  
        line.setAttribute('marker-end', `url(#arrow-marker-${idx})`);
        arrowLine.appendChild(line);
  
        
        const circle = document.createElementNS(svgNS, 'circle');
        circle.setAttribute('cx', '105');
        circle.setAttribute('cy', '0');
        circle.setAttribute('r', '6');
        circle.setAttribute('fill', 'blue');
        arrowLine.appendChild(circle);
  
        overlayDiv.appendChild(arrowLine);
  
     
        const labelBox = document.createElement('div');
        labelBox.style.position = 'absolute';
        labelBox.style.top = '0';
        labelBox.style.left = '0';
        labelBox.style.width = '180px';
        labelBox.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
        labelBox.style.border = '1px solid #ccc';
        labelBox.style.padding = '5px';
        labelBox.style.borderRadius = '4px';
        labelBox.style.fontFamily = 'sans-serif';
        labelBox.style.fontSize = '12px';
  
        
        labelBox.innerHTML = section.userData.label;
        overlayDiv.appendChild(labelBox);
  
        container.appendChild(overlayDiv);
  
        return { 
          overlayDiv, arrowLine, line, circle, labelBox 
        };
      });
  
  
      container.addEventListener('mousemove', (event) => {
        const rect = container.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;
  
        mouse.x = (x / container.clientWidth) * 2 - 1;
        mouse.y = -(y / container.clientHeight) * 2 + 1;
  
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(sections);
  
   
        sections.forEach((s) => {
          s.userData.hovered = false;
          s.material.color.setHex(s.userData.originalColor);
        });
  
        if (intersects.length > 0) {
          const sectionHover = intersects[0].object;
          sectionHover.userData.hovered = true;
          const bright = lightenColor(sectionHover.userData.originalColor, 1.8);
          sectionHover.material.color.setHex(bright);
        }
      });
  
  
      container.addEventListener('click', (event) => {
        const rect = container.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;
  
        mouse.x = (x / container.clientWidth) * 2 - 1;
        mouse.y = -(y / container.clientHeight) * 2 + 1;
  
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(sections);
  
        if (intersects.length > 0) {
          const clickedSection = intersects[0].object;
          
       
          const leadId = clickedSection.userData.leadId;
          if(leadId){
            window.location.href = `/admin/leads?view_type=table&initial_filters[columns][0][index]=stage&initial_filters[columns][0][value]=${leadId}`;;
          }

          
 
        }
      });
  
  
      function animate() {
        requestAnimationFrame(animate);
  
        sections.forEach((section, i) => {
          const ud = section.userData;
  
         
          if (ud.hovered) {
            ud.targetOffsetY = ud.offsetHoverY;
            ud.targetScale   = ud.scaleHover;
            section.rotation.x += 0.03;
          } else {
            ud.targetOffsetY = 0;
            ud.targetScale   = 1;
          }
  
        
          ud.currentOffsetY = THREE.MathUtils.lerp(
            ud.currentOffsetY,
            ud.targetOffsetY,
            0.1
          );
          section.position.y = ud.basePosition.y + ud.currentOffsetY;
  
        
          ud.currentScale = THREE.MathUtils.lerp(
            ud.currentScale,
            ud.targetScale,
            0.1
          );
          section.scale.set(ud.currentScale, ud.currentScale, ud.currentScale);
  
      
          const overlay = overlays[i];
  
         
          const worldPos = new THREE.Vector3().setFromMatrixPosition(section.matrixWorld);
          worldPos.project(camera);
  
          const screenX = (worldPos.x * 0.5 + 0.5) * container.clientWidth;
          const screenY = (-worldPos.y * 0.5 + 0.5) * container.clientHeight;
  
          if (ud.hovered) {
            overlay.overlayDiv.style.display = 'block';
  
            const offsetLabelX = 120;
            const offsetLabelY = -40;
  
            overlay.overlayDiv.style.left = screenX + 'px';
            overlay.overlayDiv.style.top  = screenY + 'px';
  
                    
            // Queremos que (0,0) sea el texto, y (offsetLabelX, offsetLabelY) sea el objeto
            overlay.line.setAttribute('x1', offsetLabelX);
            overlay.line.setAttribute('y1', offsetLabelY);
            overlay.line.setAttribute('x2', 0);
            overlay.line.setAttribute('y2', 0);

            // La esfera ahora la pones en el extremo del CONO, no del texto
            overlay.circle.setAttribute('cx', 0);
            overlay.circle.setAttribute('cy', 0);
            
            overlay.labelBox.style.left = (offsetLabelX - 10) + 'px';
            overlay.labelBox.style.top  = (offsetLabelY - 20) + 'px';
            overlay.labelBox.innerHTML  = ud.label;  // tomar de userData
            
          } else {
            overlay.overlayDiv.style.display = 'none';
          }
        });
  
        renderer.render(scene, camera);
      }
      animate();
  
    
      window.addEventListener('resize', onWindowResize);
      function onWindowResize() {
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
      }
    }, 2000);
  </script>
  