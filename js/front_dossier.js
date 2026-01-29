document.addEventListener("DOMContentLoaded", function() {
    const carouselContainer = document.getElementById('photoCarousel');
    const content = document.getElementById('carouselContent');

    if (carouselContainer && content) {
        const imagesRaw = carouselContainer.getAttribute('data-images');
        const imagesList = imagesRaw ? JSON.parse(imagesRaw) : [];
        const allItems = [...imagesList, { type: 'ADD_BUTTON' }];
        
        // MODIFICATION : 1 seule image par slide
        const itemsPerSlide = 1; 
        let finalHtml = '';

        for (let i = 0; i < allItems.length; i += itemsPerSlide) {
            const isActive = i === 0 ? 'active' : '';
            finalHtml += `<div class="carousel-item ${isActive}"><div class="row justify-content-center">`;

            const chunk = allItems.slice(i, i + itemsPerSlide);
            
            chunk.forEach((item) => {
                // Utilisation de col-12 pour prendre toute la largeur
                if (item.type === 'ADD_BUTTON') {
                    finalHtml += `
                        <div class="col-12 text-center">
                            <div class="card bg-dark border-secondary shadow-sm mx-auto custom-carousel-card" 
                                 style="cursor:pointer;" 
                                 data-bs-toggle="modal" 
                                 data-bs-target="#uploadModal">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-circle-plus"></i>
                                    <span class="ms-2">Ajouter une photo</span>
                                </div>
                            </div>
                        </div>`;
                } else {
                    finalHtml += `
                        <div class="col-12 text-center">
                            <div class="card bg-dark border-secondary shadow-sm mx-auto custom-carousel-card">
                                <img src="${item.Photo_Lien}" class="card-img-top img-large" 
                                     onclick="openLightbox('${item.Photo_Lien}', '${item.Photo_ID}')"
                                     alt="Photo">
                            </div>
                        </div>`;
                }
            });

            finalHtml += '</div></div>';
        }
        content.innerHTML = finalHtml;
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