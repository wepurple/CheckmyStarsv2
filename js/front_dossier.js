document.addEventListener("DOMContentLoaded", function() {
    const carouselContainer = document.getElementById('photoCarousel');
    const content = document.getElementById('carouselContent');

    if (carouselContainer && content) {
        const imagesRaw = carouselContainer.getAttribute('data-images');
        const imagesList = imagesRaw ? JSON.parse(imagesRaw) : [];
        
        let html = '<div class="carousel-item active"><div class="row g-3 justify-content-center">';
        
        imagesList.forEach((photo) => {
            // photo correspond à un élément du tableau (ex: l'index 0 de ton array)
            html += `
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card h-100 bg-dark border-secondary shadow-sm">
                        <img src="${photo.Photo_Lien}" class="card-img-top" 
                             style="height: 200px; object-fit: cover; border-radius: 10px; cursor: pointer;" 
                             onclick="openLightbox('${photo.Photo_Lien}', '${photo.Photo_ID}')"
                             alt="Photo">
                    </div>
                </div>`;
        });

        // Bouton "+" (toujours présent à la fin)
        html += `
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card h-100 bg-dark border-secondary shadow-sm" 
                     style="cursor:pointer; min-height: 200px;" 
                     data-bs-toggle="modal" 
                     data-bs-target="#uploadModal">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-circle-plus"></i>
                    </div>
                </div>
            </div>`;

        html += '</div></div>';
        content.innerHTML = html;
    }
});

// Fonction pour ouvrir l'image en grand
function openLightbox(lien, id) {
    document.getElementById('lightboxImg').src = lien;
    
    // Configurer le bouton de suppression à l'intérieur de la lightbox
    const btnDelete = document.getElementById('btnOpenDelete');
    btnDelete.onclick = function() {
        // Ferme la lightbox et ouvre le modal de confirmation
        bootstrap.Modal.getInstance(document.getElementById('lightboxModal')).hide();
        openDeleteModal(id, lien);
    };

    const myModal = new bootstrap.Modal(document.getElementById('lightboxModal'));
    myModal.show();
}

// Fonction pour ouvrir le modal de suppression
function openDeleteModal(id, lien) {
    document.getElementById('deletePhotoId').value = id;
    document.getElementById('previewDeleteImg').src = lien;
    const delModal = new bootstrap.Modal(document.getElementById('deletePhotoModal'));
    delModal.show();
}