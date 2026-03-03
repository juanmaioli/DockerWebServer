async function enviaForm(formName) {
    const formElement = document.getElementById(formName);
    const respuesta = document.getElementById("respuesta");
    const formData = new FormData(formElement);
    const value = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('backend.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ value })
        });

        if (!response.ok) {
            throw new Error(`Error en el servidor: ${response.status} ${response.statusText}`);
        }

        const html = await response.text();
        respuesta.innerHTML = html;
    } catch (error) {
        console.error('Error en la petición:', error);
        respuesta.innerHTML = `<div class="alert alert-danger">Error al procesar la solicitud: ${error.message}</div>`;
    }
}

async function getDatabases() {
    const db_server = document.getElementById("db_server").value;
    const db_user = document.getElementById("db_user").value;
    const db_pass = document.getElementById("db_pass").value;
    const db_serverport = document.getElementById("db_serverport").value;
    const db_name_select = document.getElementById("db_name");

    if (!db_pass) return;

    const value = {
        cmd: 'list_dbs',
        db_server,
        db_user,
        db_pass,
        db_serverport
    };

    try {
        const response = await fetch('backend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ value })
        });

        if (response.ok) {
            const html = await response.text();
            // Si el backend devuelve opciones, las inyectamos
            if (html.includes('<option')) {
                db_name_select.innerHTML = "<option value=''>Seleccionar DB</option>" + html;
            }
        }
    } catch (error) {
        console.error('Error al cargar bases de datos:', error);
    }
}