const demoTabs = document.querySelectorAll('[data-demo-tab]');
const demoViews = document.querySelectorAll('[data-demo-view]');
const modal = document.querySelector('[data-demo-modal]');
const modalTitle = document.querySelector('[data-demo-modal-title]');
const modalContent = document.querySelector('[data-demo-modal-content]');

const modalData = {
  lead: {
    title: 'Ficha de lead - Lucia Romero',
    content: `
      <div class="demo-detail-grid">
        <span><b>Telefono</b>+34 688 888 888</span>
        <span><b>Email</b>lucia.romero@example.com</span>
        <span><b>Interes</b>Prueba gratuita</span>
        <span><b>Etapa</b>Contactado</span>
      </div>
      <div class="demo-note-box">
        <b>Notas comerciales</b>
        <p>Quiere probar una clase de fuerza esta semana. Prefiere contacto por telefono por la tarde.</p>
      </div>
    `,
  },
  socio: {
    title: 'Ficha de socio - Ana Lopez',
    content: `
      <div class="demo-detail-grid">
        <span><b>Membresia</b>Profesional mensual</span>
        <span><b>Caducidad</b>29/07/2026</span>
        <span><b>Reservas</b>Yoga, Pilates, HIIT</span>
        <span><b>Estado</b>Al dia</span>
      </div>
      <div class="demo-note-box">
        <b>Historial reciente</b>
        <p>Renovo el plan mensual y tiene 3 reservas activas esta semana.</p>
      </div>
    `,
  },
  clase: {
    title: 'Clase - Yoga',
    content: `
      <div class="demo-detail-grid">
        <span><b>Fecha</b>29/06/2026</span>
        <span><b>Horario</b>09:00 - 10:00</span>
        <span><b>Aforo</b>12 ocupadas / 15</span>
        <span><b>Instructor</b>Laura Martin</span>
      </div>
      <div class="demo-note-box">
        <b>Reservas</b>
        <p>Ana Lopez, Miguel Torres, Lucia Romero y 9 socios mas. Desde la demo no se pueden anadir ni cancelar reservas.</p>
      </div>
    `,
  },
  tarea: {
    title: 'Tarea - Renovar membresia',
    content: `
      <div class="demo-detail-grid">
        <span><b>Tipo</b>Retencion</span>
        <span><b>Responsable</b>Laura Martin</span>
        <span><b>Socio</b>Miguel Torres</span>
        <span><b>Estado</b>Pendiente</span>
      </div>
      <div class="demo-note-box">
        <b>Descripcion</b>
        <p>Contactar antes del vencimiento para renovar la membresia basica mensual.</p>
      </div>
    `,
  },
};

function activateTab(tabName) {
  demoTabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.demoTab === tabName));
  demoViews.forEach((view) => view.classList.toggle('active', view.dataset.demoView === tabName));
}

function openModal(type) {
  const data = modalData[type] || modalData.lead;
  modalTitle.textContent = data.title;
  modalContent.innerHTML = data.content;
  modal.hidden = false;
  document.body.classList.add('modal-open');
}

function closeModal() {
  modal.hidden = true;
  document.body.classList.remove('modal-open');
}

demoTabs.forEach((tab) => {
  tab.addEventListener('click', () => activateTab(tab.dataset.demoTab));
});

document.querySelectorAll('[data-open-demo-modal]').forEach((trigger) => {
  trigger.addEventListener('click', () => openModal(trigger.dataset.openDemoModal));
});

document.querySelectorAll('[data-close-demo-modal]').forEach((trigger) => {
  trigger.addEventListener('click', closeModal);
});

modal?.addEventListener('click', (event) => {
  if (event.target === modal) {
    closeModal();
  }
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape' && modal && !modal.hidden) {
    closeModal();
  }
});
