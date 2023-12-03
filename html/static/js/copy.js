function copy(selector) {
    let element = document.querySelector(selector);
    navigator.clipboard.writeText(element.innerHTML);

    return false;
}