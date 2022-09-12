const transitionEndedClass = 'ibexa-selenium-transition-ended';
const transitionStartedClass = 'ibexa-selenium-transition-started';
document.addEventListener('transitionstart', (event) => {
    event.target.classList.add(transitionStartedClass)
    event.target.classList.remove(transitionEndedClass)
});
document.addEventListener('transitionend', (event) => {
    event.target.classList.add(transitionEndedClass)
    event.target.classList.remove(transitionStartedClass)
});
