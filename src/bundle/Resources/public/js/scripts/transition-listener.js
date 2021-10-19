const transitionEndedClass = 'ibexa-selenium-transition-ended';
document.addEventListener('transitionstart', (event) => event.target.classList.remove(transitionEndedClass));
document.addEventListener('transitionend', (event) => event.target.classList.add(transitionEndedClass));
