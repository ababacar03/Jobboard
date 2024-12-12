// Fonction pour charger le formulaire d'ajout d'annonce
function loadAdvertisementForm() {
    const formHTML = `
        <h2>Ajouter une annonce</h2>
        <form id="add-advertisement-form" method="POST">
            <input type="text" name="title" placeholder="Titre" required>
            <textarea name="short_description" placeholder="Description courte" required></textarea>
            <textarea name="learn_more" placeholder="En savoir plus" required></textarea>
            <input type="number" name="wage" placeholder="Salaire" required>
            <input type="text" name="place" placeholder="Lieu" required>
            <input type="text" name="working_time" placeholder="Temps de travail" required>
            <input type="hidden" name="company_id" value="1"> <!-- Changez selon votre logique -->
            <button type="submit" name="add_advertisement">Ajouter l'annonce</button>
        </form>
    `;

    document.getElementById('advertisement-form').innerHTML = formHTML;
}

// Fonction pour modifier une annonce
function editAdvertisement(id, title, short_description, learn_more, wage, place, working_time) {
    const editHTML = `
        <h2>Modifier l'annonce</h2>
        <form id="edit-advertisement-form" method="POST">
            <input type="hidden" name="ad_id" value="${id}">
            <input type="text" name="title" value="${title}" required>
            <textarea name="short_description" required>${short_description}</textarea>
            <textarea name="learn_more" required>${learn_more}</textarea>
            <input type="number" name="wage" value="${wage}" required>
            <input type="text" name="place" value="${place}" required>
            <input type="text" name="working_time" value="${working_time}" required>
            <button type="submit" name="edit_advertisement">Modifier l'annonce</button>
        </form>
    `;

    document.getElementById('advertisement-form').innerHTML = editHTML;
}

// Charger le formulaire d'ajout d'annonce au chargement de la page
window.onload = loadAdvertisementForm;
