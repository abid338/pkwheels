function toggleFields() {
  const category = document.getElementById("category").value;
  const featuresSection = document.getElementById("featuresSection");
  const vehicleLabel = document.getElementById("vehicle-label");
  const descLabel = document.getElementById("desc-label");
  const featuresTitle = document.getElementById("features-title");
  const engineTypeSelect = document.getElementById("engine_type");
  const colorSelect = document.getElementById("color");
  const featuresContainer = document.getElementById("featuresContainer");

  // Clear existing options
  engineTypeSelect.innerHTML = '<option value="">Select Engine Type</option>';
  colorSelect.innerHTML = '<option value="">Select Color</option>';
  featuresContainer.innerHTML = "";

  if (category === "car") {
    featuresSection.style.display = "block";
    vehicleLabel.textContent = "Car";
    descLabel.textContent = "Car";
    featuresTitle.textContent = "Car";

    // Add car engine types
    window.carEngines.forEach(function (engine) {
      const option = document.createElement("option");
      option.value = engine;
      option.textContent = engine;
      engineTypeSelect.appendChild(option);
    });

    // Add car colors
    window.carColors.forEach(function (color) {
      const option = document.createElement("option");
      option.value = color;
      option.textContent = color;
      colorSelect.appendChild(option);
    });

    // Add car features
    Object.keys(window.carFeatures).forEach(function (key) {
      const div = document.createElement("div");
      div.className = "col-md-3 mb-3";
      div.innerHTML = `
                <div class="custom-checkbox">
                    <input class="form-check-input" type="checkbox" name="${key}" id="${key}">
                    <label class="form-check-label" for="${key}">
                        ${window.carFeatures[key]}
                    </label>
                </div>
            `;
      featuresContainer.appendChild(div);
    });
  } else if (category === "bike") {
    featuresSection.style.display = "block";
    vehicleLabel.textContent = "Bike";
    descLabel.textContent = "Bike";
    featuresTitle.textContent = "Bike";

    // Add bike engine types
    window.bikeEngines.forEach(function (engine) {
      const option = document.createElement("option");
      option.value = engine;
      option.textContent = engine;
      engineTypeSelect.appendChild(option);
    });

    // Add bike colors
    window.bikeColors.forEach(function (color) {
      const option = document.createElement("option");
      option.value = color;
      option.textContent = color;
      colorSelect.appendChild(option);
    });

    // Add bike features
    Object.keys(window.bikeFeatures).forEach(function (key) {
      const div = document.createElement("div");
      div.className = "col-md-4 mb-3";
      div.innerHTML = `
                <div class="custom-checkbox">
                    <input class="form-check-input" type="checkbox" name="${key}" id="${key}">
                    <label class="form-check-label" for="${key}">
                        ${window.bikeFeatures[key]}
                    </label>
                </div>
            `;
      featuresContainer.appendChild(div);
    });
  } else {
    featuresSection.style.display = "none";
    vehicleLabel.textContent = "Vehicle";
    descLabel.textContent = "Vehicle";
  }
}

// Description Character Counter
document.addEventListener("DOMContentLoaded", function () {
  const descriptionTextarea = document.querySelector(
    'textarea[name="description"]',
  );
  if (descriptionTextarea) {
    descriptionTextarea.addEventListener("input", function () {
      const maxLength = 1000;
      const remaining = maxLength - this.value.length;
      document.getElementById("charCount").textContent = remaining;
    });
  }

  // Image File Size Validation
  document.querySelectorAll(".image-input").forEach(function (input) {
    input.addEventListener("change", function () {
      const maxSize = window.imageMaxSize;
      const file = this.files[0];
      const infoElement = document.getElementById(
        "size-info-" + this.name.split("_")[1],
      );

      if (file) {
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
        if (file.size > maxSize) {
          infoElement.textContent =
            "File too large: " +
            fileSizeMB +
            "MB (max " +
            window.imageMaxSizeMB +
            "MB)";
          infoElement.classList.add("text-danger");
          this.value = "";
        } else {
          infoElement.textContent = "Size: " + fileSizeMB + "MB";
          infoElement.classList.remove("text-danger");
          infoElement.classList.add("text-success");
        }
      }
    });
  });
});
