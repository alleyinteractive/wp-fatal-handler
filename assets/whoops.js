/**
 * Floating circle button to re-open Whoops after it was hidden.
 */

const whoopsDevtoolsBtn = document.createElement('div');
whoopsDevtoolsBtn.id = 'whoops-devtools-btn';
whoopsDevtoolsBtn.style.position = 'fixed';
whoopsDevtoolsBtn.style.bottom = '24px';
whoopsDevtoolsBtn.style.left = '24px';
whoopsDevtoolsBtn.style.width = '48px';
whoopsDevtoolsBtn.style.height = '48px';
whoopsDevtoolsBtn.style.background = 'linear-gradient(135deg, #ff5e62 0%, #ff9966 100%)';
whoopsDevtoolsBtn.style.borderRadius = '50%';
whoopsDevtoolsBtn.style.boxShadow = '0 2px 12px rgba(0,0,0,0.18)';
whoopsDevtoolsBtn.style.display = 'flex';
whoopsDevtoolsBtn.style.alignItems = 'center';
whoopsDevtoolsBtn.style.justifyContent = 'center';
whoopsDevtoolsBtn.style.cursor = 'pointer';
whoopsDevtoolsBtn.style.zIndex = '9999';
whoopsDevtoolsBtn.style.transition = 'box-shadow 0.2s';
whoopsDevtoolsBtn.title = 'Open Whoops Devtools';

whoopsDevtoolsBtn.innerHTML = '<span style="color:white;font-size:2rem;font-weight:bold;user-select:none;">!</span>';

whoopsDevtoolsBtn.addEventListener('mouseenter', () => {
  whoopsDevtoolsBtn.style.boxShadow = '0 4px 24px rgba(0,0,0,0.28)';
});
whoopsDevtoolsBtn.addEventListener('mouseleave', () => {
  whoopsDevtoolsBtn.style.boxShadow = '0 2px 12px rgba(0,0,0,0.18)';
});

document.body.appendChild(whoopsDevtoolsBtn);

whoopsDevtoolsBtn.addEventListener('click', () => {
  const whoopsContainer = document.querySelector('.Whoops.container');
  if (whoopsContainer && whoopsContainer.style.display === 'none') {
    whoopsContainer.style.display = '';
  }
});
