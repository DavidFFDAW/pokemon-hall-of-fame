function handleAskConfirmation(event) {
    const element = event.currentTarget;
    const message = element.dataset.confirm;
    if (!confirm(message)) event.preventDefault();
}

function onFlashAnimationEnd(event) {
    const flash = event.target;
    if (event.animationName !== 'fadeOut') return;
    flash.removeEventListener('animationend', onFlashAnimationEnd);
    flash.remove();
}

function createFlashMessage(message, type = 'info') {
    console.log('Creating flash message:', message, 'Type:', type);
    const flashList = document.querySelector('ul.flash-messages');
    if (!flashList) return;

    const flashItem = document.createElement('li');
    flashItem.classList.add('flash-message', type);
    flashItem.textContent = message;
    flashItem.addEventListener('animationend', onFlashAnimationEnd);
    flashList.appendChild(flashItem);
}

function setBodyLoading(setter) {
    document.body.classList.toggle('is_loading', setter);
}

async function http(method, url, data) {
    try {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
            },
        };
        if (method === 'post' && data) options.body = data instanceof FormData ? data : JSON.stringify(data);

        setBodyLoading(true);
        const response = await fetch(url, options);
        if (!response.ok) {
            const errorText = await response.text();
            return {
                error: true,
                success: false,
                message: errorText || 'An error occurred while processing the request.',
            };
        }
        const content = await response.json();
        return { error: false, success: true, message: content.message || 'Request successful.' };
    } catch (error) {
        console.error('HTTP request failed');
        return { error: true, success: false, message: 'An error occurred while processing the request.' };
    } finally {
        setBodyLoading(false);
    }
}

function setAsyncForm(form) {
    form.addEventListener('submit', async event => {
        event.preventDefault();
        const action = form.action || window.location.href;
        const method = form.method.toUpperCase();
        const redirect = form.dataset.redirect || '';
        const formData = new FormData(form);
        const response = await http(method, action, formData);
        const errorType = response.error ? 'error' : 'success';

        if (response.success) form.reset();
        if (response.message) createFlashMessage(response.message, errorType);
        if (redirect) window.location.href = redirect;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const confirmationLinks = document.querySelectorAll('a[data-confirm]');
    const confirmationBtns = document.querySelectorAll('button[data-confirm]');
    const flashMessages = document.querySelectorAll('ul.flash-messages li.flash-message');

    const asyncForms = document.querySelectorAll('form[data-async]');
    const confirmations = [...confirmationLinks, ...confirmationBtns];

    if (flashMessages.length > 0 || confirmations.length > 0) {
        for (const flash of flashMessages) {
            flash.addEventListener('animationend', onFlashAnimationEnd);
        }
        for (const confirmation of confirmations) {
            confirmation.addEventListener('click', handleAskConfirmation);
        }
    }

    if (asyncForms.length > 0) {
        asyncForms.forEach(setAsyncForm);
    }
});
