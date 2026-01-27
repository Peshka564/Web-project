/**
 * @type {HTMLDialogElement}
 */
const saveModal = document.getElementById("save-modal");
/**
 * @type {HTMLTextAreaElement}
 */
const converterInput = document.getElementById("converter-input");
/**
 * @type {HTMLTextAreaElement}
 */
const sExprInput = document.getElementById("s-expr-input");
/**
 * @type {HTMLInputElement}
 */
const outputLanguage = document.getElementById("output-language");

const convertFormId = "convert-form"
const saveFormId = "save-form"


const observer = new MutationObserver(() => {
  if (saveModal.open) {
    converterInput.setAttribute("form",saveFormId);
    sExprInput.setAttribute("form",saveFormId);
    outputLanguage.setAttribute("form",saveFormId);
  } else {
    converterInput.setAttribute("form",convertFormId);
    sExprInput.setAttribute("form",convertFormId);
    outputLanguage.setAttribute("form",convertFormId);
  }
});


observer.observe(saveModal, {
  attributes: true,
  attributeFilter: ['open']
});


