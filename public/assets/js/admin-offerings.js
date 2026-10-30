(() => {
    const service = document.querySelector('#offering-service');
    const cameraField = document.querySelector('#camera-count-field');
    const pricingType = document.querySelector('#offering-pricing-type');
    const priceField = document.querySelector('#offering-price-field');
    const priceInput = priceField?.querySelector('input[name="price"]');

    const updateCameraField = () => {
        if (service && cameraField) {
            cameraField.hidden = service.value !== 'camaras-de-seguridad';
        }
    };

    const updatePriceField = () => {
        if (!pricingType || !priceField || !priceInput) return;

        const requiresPrice = pricingType.value !== 'quote';
        priceField.hidden = !requiresPrice;
        priceInput.required = requiresPrice;
    };

    service?.addEventListener('change', updateCameraField);
    pricingType?.addEventListener('change', updatePriceField);
    updateCameraField();
    updatePriceField();
})();
