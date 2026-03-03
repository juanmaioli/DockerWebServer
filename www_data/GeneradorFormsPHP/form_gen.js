function selectElement(campo, tipo) {
    let columna = document.getElementById(campo);
    let columnaPhp = document.getElementById(campo + "_php");
    let columnaSql = document.getElementById(campo + "_sql");
    let campoSeleccionado = document.getElementById(campo + "Tr");
    let inputSeleccionado = document.getElementById(campo + tipo);
    let checkBootstrap = document.getElementById("bootstrap");
    let classBootstrap, classBootstrapCheck, classBootstrapRange, classBootstrapColor, classBootstrapBtn;

    clearElement(campo);
    
    if (checkBootstrap.checked === true) {
        classBootstrap = "class='form-control text-primary' ";
        classBootstrapCheck = "class='form-check-input' ";
        classBootstrapRange = "class='form-range' ";
        classBootstrapColor = "class='form-control form-control-color' ";
        classBootstrapBtn = "class='btn btn-primary' ";
    } else {
        classBootstrap = "";
        classBootstrapCheck = "";
        classBootstrapRange = "";
        classBootstrapColor = "";
        classBootstrapBtn = "";
    }

    campoSeleccionado.classList.add("border", "border-danger", "border-2");
    inputSeleccionado.classList.add("text-primary", "fw-bold");

    let inputTag = "";
    switch (tipo) {
        case 'text':
            inputTag = `&lt;input type='text' ${classBootstrap} id='${campo}' name='${campo}' value = '${campo}' placeholder='${campo}' title='${campo}'&gt;`
            break;
        case 'number':
            inputTag = `&lt;input type='number' ${classBootstrap} id='${campo}' name='${campo}' value = '0' placeholder='${campo}' title='${campo}'&gt;`
            break;
        case 'hidden':
            inputTag = `&lt;input type='hidden' id='${campo}' name='${campo}' value = '${campo}' title='${campo}'&gt;`
            break;
        case 'password':
            inputTag = `&lt;input type='password' ${classBootstrap} id='${campo}' name='${campo}' value = '' placeholder='${campo}' title='${campo}'&gt;`
            break;
        case 'checkbox':
            inputTag = `&lt;input type='checkbox' ${classBootstrapCheck} id='${campo}' name='${campo}' value = '' placeholder='${campo}' title='${campo}'&gt; Label: ${campo}`
            break;
        case 'radio':
            inputTag = `&lt;input type='radio' ${classBootstrapCheck} id='${campo}' name='${campo}' value = '' placeholder='${campo}' title='${campo}'&gt; Label: ${campo}`
            break;
        case 'date':
            inputTag = `&lt;input type='date' ${classBootstrap} id='${campo}' name='${campo}' value = '2022-02-22' title='${campo}'&gt;`
            break;
        case 'file':
            inputTag = `&lt;input type='file' ${classBootstrap} id='${campo}' name='${campo}' accept='.pdf, .jpeg, .jpg' title='${campo}'&gt;`
            break;
        case 'range':
            inputTag = `&lt;input type='range' ${classBootstrapRange} id='${campo}' name='${campo}' min='0' max='10' step='1' value='3' title='${campo}'&gt;`
            break;
        case 'color':
            inputTag = `&lt;input type='color' ${classBootstrapColor} id='${campo}' name='${campo}' value = '#563d7c' title='${campo}'&gt;`
            break;
        case 'button':
            inputTag = `&lt;input type='button' ${classBootstrapBtn} id='${campo}' name='${campo}' value = '${campo}' onclick="alert('OnClick Action')" title='${campo}'&gt;<br>`
            inputTag += `&lt;input type='reset' ${classBootstrapBtn} id='${campo}' name='${campo}' value = 'reset' title='${campo}'&gt;<br>`
            inputTag += `&lt;input type='submit' ${classBootstrapBtn} id='${campo}' name='${campo}' value = 'Enviar' title='${campo}'&gt;`
            break;
        case 'select':
            inputTag = `&lt;select name='${campo}' id='${campo}' ${classBootstrap} onchange='this.form.submit()'&gt;<br>
                    &lt;option value='' selected&gt;Seleccionar item&lt;/option&gt;<br>
                    &lt;option value='1'&gt;Item 1&lt;/option&gt;<br>
                    &lt;option value='2'&gt;Item 2&lt;/option&gt;<br>
                    &lt;/select&gt;`
            break;
        case 'textarea':
            inputTag = `&lt;textarea id='${campo}' ${classBootstrap} name='${campo}' rows='5' cols='10' placeholder='${campo}' title='${campo}'&gt;${campo}&lt;/textarea&gt;`
            break;
        default:
            inputTag = "";
    }
    
    columna.innerHTML = inputTag;
    columnaPhp.innerHTML = `$${campo} = $_POST["${campo}"];`;
    columnaSql.innerHTML = campo; // Guardamos el nombre del campo para el SQL

    actualizarSqlFull();
}

function clearElement(campo) {
    const inputLink = ["text", "number", "hidden", "password", "checkbox", "radio", "date", "file", "range", "color", "button", "select", "textarea"];
    let columna = document.getElementById(campo);
    let columnaPhp = document.getElementById(campo + "_php");
    let columnaSql = document.getElementById(campo + "_sql");
    let campoSeleccionado = document.getElementById(campo + "Tr");
    
    if (campoSeleccionado) {
        campoSeleccionado.classList.remove("border", "border-danger", "border-2");
        inputLink.forEach(tipo => {
            let el = document.getElementById(campo + tipo);
            if (el) el.classList.remove("text-primary", "fw-bold");
        });
    }

    if (columna) columna.innerHTML = "";
    if (columnaPhp) columnaPhp.innerHTML = "";
    if (columnaSql) columnaSql.innerHTML = "";

    actualizarSqlFull();
}

function actualizarSqlFull() {
    let tableName = document.getElementById("table_name") ? document.getElementById("table_name").value : "tabla";
    let sqlTextArea = document.getElementById("sql_full");
    if (!sqlTextArea) return;

    // Obtener todos los campos que tienen contenido en su columna SQL
    let camposSql = Array.from(document.querySelectorAll('[id$="_sql"]'))
                         .filter(el => el.innerHTML.trim() !== "")
                         .map(el => el.innerHTML.trim());

    if (camposSql.length === 0) {
        sqlTextArea.value = "";
        return;
    }

    let fields = camposSql.join(", ");
    let values = camposSql.map(c => ":" + c).join(", ");
    
    sqlTextArea.value = `INSERT INTO ${tableName} (${fields}) \nVALUES (${values});`;
}

function getColumnText(type) {
    let text = "";
    let selector = "";
    
    switch(type) {
        case 'html': selector = '[id]:not([id$="_php"]):not([id$="_sql"]):not([id$="Tr"]):not([id^="db_"]):not([id="respuesta"]):not([id="sql_full"])'; break;
        case 'php': selector = '[id$="_php"]'; break;
        case 'sql': selector = '[id$="_sql"]'; break;
    }
    
    if (selector) {
        const elements = Array.from(document.querySelectorAll(selector))
                              .filter(el => el.innerText.trim() !== "" && el.closest('#respuesta'));
        
        text = elements.map(el => el.innerText.trim()).join("\n");
        text = text.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
    }
    return text;
}

function copyToClipboard(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return;
    let text = el.tagName === 'TEXTAREA' || el.tagName === 'INPUT' ? el.value : el.innerText;
    
    text = text.replace(/&lt;/g, '<').replace(/&gt;/g, '>');

    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = "¡Copiado!";
        btn.classList.replace('btn-info', 'btn-success');
        
        setTimeout(() => {
            btn.innerText = originalText;
            btn.classList.replace('btn-success', 'btn-info');
        }, 2000);
    });
}

function copyColumn(type) {
    let text = getColumnText(type);
    if (text) {
        navigator.clipboard.writeText(text).then(() => {
            const btn = event.target;
            const originalText = btn.innerText;
            btn.innerText = "¡Copiado!";
            btn.classList.replace('btn-outline-success', 'btn-success');
            btn.classList.replace('btn-outline-danger', 'btn-success');
            btn.classList.replace('btn-outline-info', 'btn-success');
            
            setTimeout(() => {
                btn.innerText = originalText;
                btn.classList.replace('btn-success', 'btn-outline-success');
                btn.classList.replace('btn-success', 'btn-outline-danger');
                btn.classList.replace('btn-success', 'btn-outline-info');
            }, 2000);
        });
    }
}

function downloadColumn(type) {
    let text = getColumnText(type);
    if (type === 'sql') {
        const sqlFull = document.getElementById("sql_full");
        if (sqlFull && sqlFull.value) text = sqlFull.value;
    }
    
    if (!text) return;

    const tableName = document.getElementById("table_name").value || "archivo";
    const filename = `${tableName}.${type === 'sql' ? 'sql' : type}`;
    const blob = new Blob([text], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    
    a.style.display = 'none';
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}