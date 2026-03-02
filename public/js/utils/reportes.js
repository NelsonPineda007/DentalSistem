/**
 * Módulo de Reportes PDF Modular - Versión Corregida (Sin Glitch de Texto)
 */
const ReportePDF = {
    generar: function(config) {
        if (!window.jspdf) { alert("Librería jsPDF no encontrada"); return; }
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        const colors = {
            primary: [30, 64, 175],   
            text: [30, 30, 30],       
            lightGray: [241, 245, 249]
        };

        const margin = 20;
        let y = 0;

        // 1. ENCABEZADO
        doc.setFillColor(...colors.primary);
        doc.rect(0, 0, 210, 40, 'F');
        
        doc.setTextColor(255, 255, 255);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(24);
        doc.text("DENTISTA", margin, 20);
        
        doc.setFontSize(12);
        doc.setFont("helvetica", "normal");
        doc.text("Historial Clínico del Paciente", margin, 30);
        
        // Datos cabecera derecha
        doc.setFontSize(10);
        doc.text(`Fecha: ${new Date().toLocaleDateString()}`, 150, 20);
        if(config.folio) doc.text(`N° Exp: ${config.folio}`, 150, 28);

        y = 55;

        // --- HELPERS ---
        const drawSectionTitle = (title) => {
            if (y > 270) { doc.addPage(); y = 20; }
            doc.setFillColor(...colors.lightGray);
            doc.rect(margin, y, 170, 8, 'F');
            doc.setTextColor(...colors.primary);
            doc.setFont("helvetica", "bold");
            doc.setFontSize(11);
            doc.text(title.toUpperCase(), margin + 5, y + 5.5);
            y += 15;
        };

        // Función para pintar un dato (Label: Valor) en una posición específica
        const drawDataPoint = (label, value, xPos, yPos) => {
            doc.setTextColor(...colors.text);
            doc.setFontSize(10);
            doc.setFont("helvetica", "bold");
            doc.text(`${label}:`, xPos, yPos);
            
            doc.setFont("helvetica", "normal");
            const valStr = value ? String(value) : '-';
            // Ajuste simple: Label + espacio fijo para valor
            doc.text(valStr, xPos + 40, yPos);
        };

        // --- A. INFORMACIÓN PERSONAL (REHECHO PARA EVITAR SUPERPOSICIÓN) ---
        drawSectionTitle("Información Personal");
        
        // Usamos coordenadas fijas para evitar el error de cálculo
        // Columna Izquierda (X = margin = 20)
        drawDataPoint("Nombre", config.data.nombre, margin, y);
        drawDataPoint("Nacimiento", config.data.nacimiento, margin, y + 6);
        drawDataPoint("Edad", config.data.edad, margin, y + 12);
        drawDataPoint("Género", config.data.genero, margin, y + 18);

        // Columna Derecha (X = 120) - Mismas alturas Y
        drawDataPoint("Teléfono", config.data.telefono, 120, y);
        drawDataPoint("Email", config.data.email, 120, y + 6);
        drawDataPoint("Cod. Postal", config.data.cp, 120, y + 12);
        
        // Bajamos Y para la dirección (que es ancha)
        y += 26; 
        
        // Dirección (Ancho completo)
        doc.setFont("helvetica", "bold");
        doc.text("Dirección:", margin, y);
        doc.setFont("helvetica", "normal");
        const direccionLines = doc.splitTextToSize(config.data.direccion || '-', 130);
        doc.text(direccionLines, margin + 40, y);
        
        y += (direccionLines.length * 6) + 8; // Espacio dinámico + margen

        // --- B. CONTACTO EMERGENCIA ---
        drawSectionTitle("Contacto de Emergencia");
        drawDataPoint("Nombre", config.data.emergencia_nombre, margin, y);
        drawDataPoint("Teléfono", config.data.emergencia_tel, 120, y);
        y += 12;

        // --- C. INFORMACIÓN MÉDICA ---
        drawSectionTitle("Información Médica");
        
        const drawMedicalField = (label, value) => {
            doc.setFont("helvetica", "bold");
            doc.text(`${label}:`, margin, y);
            
            doc.setFont("helvetica", "normal");
            const valStr = value ? String(value) : 'Ninguno';
            
            // Si el texto es largo, lo envolvemos
            if (valStr.length > 60) {
                const lines = doc.splitTextToSize(valStr, 120);
                doc.text(lines, margin + 45, y);
                y += (lines.length * 5) + 2;
            } else {
                doc.text(valStr, margin + 45, y);
                y += 6;
            }
        };

        drawMedicalField("Seguro Médico", config.data.seguro);
        drawMedicalField("Alergias", config.data.alergias);
        drawMedicalField("Enf. Crónicas", config.data.cronicas);
        drawMedicalField("Medicamentos", config.data.medicamentos);
        if(config.data.notas) {
            y+=2;
            drawMedicalField("Notas", config.data.notas);
        }
        y += 5;

        // --- D. HISTORIAL DE CITAS ---
        if (config.citas && config.citas.length > 0) {
            drawSectionTitle("Historial de Citas");
            config.citas.forEach((cita, i) => {
                if (y > 270) { doc.addPage(); y = 20; }
                doc.setFont("helvetica", "bold");
                doc.text(`${i + 1}. ${cita.fecha}`, margin, y);
                doc.setFont("helvetica", "normal");
                doc.text(`- ${cita.motivo} (${cita.estado})`, margin + 40, y);
                y += 7;
            });
            y += 5;
        }

        // --- E. TRATAMIENTOS ---
        if (config.tratamientos && config.tratamientos.length > 0) {
            drawSectionTitle("Tratamientos Realizados");
            config.tratamientos.forEach((t, i) => {
                if (y > 270) { doc.addPage(); y = 20; }
                doc.setFont("helvetica", "bold");
                doc.text(`${i + 1}. ${t.nombre}`, margin, y);
                
                doc.setFont("helvetica", "normal");
                doc.text(`Fecha: ${t.fecha}`, margin + 60, y);
                doc.text(`Costo: ${t.costo}`, 150, y); // Alineado a la derecha
                y += 7;
            });
        }

        // PIE DE PÁGINA
        const pages = doc.internal.getNumberOfPages();
        for(let i=1; i<=pages; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.text(`Página ${i} de ${pages} | Generado por Sistema Dentista`, 105, 290, { align: 'center' });
        }

        doc.save(`${config.nombreArchivo}.pdf`);
    }
};