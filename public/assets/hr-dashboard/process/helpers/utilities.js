function presentModal(element, state = 'show') {
    $(`#${element}`).modal(state);
}