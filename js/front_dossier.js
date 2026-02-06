document.addEventListener("DOMContentLoaded", function() {
    const carouselContainer = document.getElementById('photoCarousel');
    const content = document.getElementById('carouselContent');

    // Initialisation des instances de modales pour éviter les doublons
    const lightboxEl = document.getElementById('lightboxModal');
    const deleteEl = document.getElementById('deletePhotoModal');
    
    // On crée les instances une seule fois au chargement
    const lightboxModal = lightboxEl ? new bootstrap.Modal(lightboxEl) : null;
    const deleteModal = deleteEl ? new bootstrap.Modal(deleteEl) : null;

    if (carouselContainer && content) {
        const imagesRaw = carouselContainer.getAttribute('data-images');
        const imagesList = imagesRaw ? JSON.parse(imagesRaw) : [];
        const allItems = [...imagesList, { type: 'ADD_BUTTON' }];
        
        const itemsPerSlide = 1; 
        let finalHtml = '';

        for (let i = 0; i < allItems.length; i += itemsPerSlide) {
            const isActive = i === 0 ? 'active' : '';
            finalHtml += `<div class="carousel-item ${isActive}"><div class="row justify-content-center">`;

            const chunk = allItems.slice(i, i + itemsPerSlide);
            
            chunk.forEach((item) => {
                if (item.type === 'ADD_BUTTON') {
                    finalHtml += `
                        <div class="col-12 text-center">
                            <div class="card bg-dark border-secondary shadow-sm mx-auto custom-carousel-card" 
                                 style="cursor:pointer;" 
                                 data-bs-toggle="modal" 
                                 data-bs-target="#uploadModal">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <i class="fa-solid fa-circle-plus fa-2x mb-2"></i>
                                    <span>Ajouter une photo</span>
                                </div>
                            </div>
                        </div>`;
                } else {
                    finalHtml += `
                        <div class="col-12 text-center">
                            <div class="card bg-dark border-secondary shadow-sm mx-auto custom-carousel-card">
                                <img src="${item.Photo_Lien}" class="card-img-top img-large" 
                                     style="cursor:zoom-in; object-fit: cover; height: 300px;"
                                     onclick="handleOpenLightbox('${item.Photo_Lien}', '${item.Photo_ID}')"
                                     alt="Photo">
                            </div>
                        </div>`;
                }
            });

            finalHtml += '</div></div>';
        }
        content.innerHTML = finalHtml;
    }

    // Fonctions attachées au scope global (window) pour l'attribut onclick
    window.handleOpenLightbox = function(lien, id) {
        document.getElementById('lightboxImg').src = lien;
        
        const btnDelete = document.getElementById('btnOpenDelete');
        btnDelete.onclick = function() {
            lightboxModal.hide();
            handleOpenDeleteModal(id, lien);
        };

        lightboxModal.show();
    };

    window.handleOpenDeleteModal = function(id, lien) {
        document.getElementById('deletePhotoId').value = id;
        document.getElementById('previewDeleteImg').src = lien;
        deleteModal.show();
    };
});