const votarButtons = document.querySelectorAll(".votar");

votarButtons.forEach((button) => {
    button.addEventListener("click", async (event) => {
        boton = event.target;
        disfraz = boton.closest(".disfraz");
        nombre = disfraz.querySelector(".nombre");
        
        form = new FormData();
        form.append("nombre", nombre.textContent);

        fetch("api/votar.php", {
            method: "POST",
            body: form
        }).then(async (datos)=>{
            respuesta =  await datos.json();
            if(respuesta.message === "Voto registrado con éxito.") {
                const votosElement = disfraz.querySelector(".votos");
                let votos = parseInt(votosElement.textContent.replace(/[^\d]/g, ""));
                votos++;
                votosElement.innerHTML = `<span>Votos:</span> ${votos}`;
                boton.disabled = true;
                alert("¡Gracias por tu voto!");
            } else if(respuesta.message === "Ya has votado por este disfraz.") {
                alert("Ya había registrado su voto");
            } else {
                alert("Hubo un problema al registrar tu voto. Intenta nuevamente.");
            }
        });
    });
});
