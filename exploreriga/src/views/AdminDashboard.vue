<template>
  <div class="admin-dashboard">
    <div class="dashboard-header">
      <h2><i class="fas fa-cog"></i> Admin Dashboard</h2>
      <p class="subtitle">Manage museums, restaurants, and places</p>
    </div>

    <div class="form-container">
      <!-- Add Museum Form -->
      <div class="form-card">
        <h3><i class="fas fa-landmark"></i> Add a Museum</h3>
        <form @submit.prevent="addMuseum" class="place-form">
          <div class="form-group">
            <label>Museum Name:</label>
            <input type="text" v-model="museumData.name" required class="form-input" />
          </div>
          <div class="form-group">
            <label>Description:</label>
            <textarea v-model="museumData.description" required class="form-textarea"></textarea>
          </div>
          <div class="form-group">
            <label>Address:</label>
            <input type="text" v-model="museumData.address" required class="form-input" />
          </div>
          <div class="form-group">
            <label>Work Hours:</label>
            <input type="text" v-model="museumData.work_hours" required class="form-input" />
          </div>
          <button type="submit" class="submit-btn museum-btn">
            <i class="fas fa-plus-circle"></i> Add Museum
          </button>
        </form>
      </div>

      <!-- Add Restaurant Form -->
      <div class="form-card">
        <h3><i class="fas fa-utensils"></i> Add a Restaurant</h3>
        <form @submit.prevent="addRestaurant" class="place-form">
          <div class="form-group">
            <label>Restaurant Name:</label>
            <input type="text" v-model="restaurantData.name" required class="form-input" />
          </div>
          <div class="form-group">
            <label>Description:</label>
            <textarea v-model="restaurantData.description" required class="form-textarea"></textarea>
          </div>
          <div class="form-group">
            <label>Address:</label>
            <input type="text" v-model="restaurantData.address" required class="form-input" />
          </div>
          <div class="form-group">
            <label>Average Price:</label>
            <input type="number" step="0.01" v-model="restaurantData.avg_price" required class="form-input" />
          </div>
          <button type="submit" class="submit-btn restaurant-btn">
            <i class="fas fa-plus-circle"></i> Add Restaurant
          </button>
        </form>
      </div>

      <!-- Add Place Form -->
      <div class="form-card">
        <h3><i class="fas fa-map-marker-alt"></i> Add a Place</h3>
        <form @submit.prevent="addPlace" class="place-form">
          <div class="form-group">
            <label>Place Name:</label>
            <input type="text" v-model="placeData.name" required class="form-input" />
          </div>
          <div class="form-group">
            <label>Description:</label>
            <textarea v-model="placeData.description" required class="form-textarea"></textarea>
          </div>
          <div class="form-group">
            <label>Address:</label>
            <input type="text" v-model="placeData.address" required class="form-input" />
          </div>
          <div class="form-group">
            <label>Facts:</label>
            <textarea v-model="placeData.facts" required class="form-textarea"></textarea>
          </div>
          <button type="submit" class="submit-btn place-btn">
            <i class="fas fa-plus-circle"></i> Add Place
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      museumData: {
        name: "",
        description: "",
        address: "",
        work_hours: ""
      },
      restaurantData: {
        name: "",
        description: "",
        address: "",
        avg_price: null
      },
      placeData: {
        name: "",
        description: "",
        address: "",
        facts: ""
      }
    };
  },
  methods: {
    async addMuseum() {
  try {
    const objectResponse = await axios.post("http://localhost:3000/add_objects.php", {
      name: this.museumData.name,
      description: this.museumData.description,
      address: this.museumData.address
    });
    console.log("Object create response:", objectResponse.data);

    if (!objectResponse.data.objects_id) {
      throw new Error("Missing objects_id in response");
    }

    const museumResponse = await axios.post("http://localhost:3000/add_museums.php", {
      objects_id: objectResponse.data.objects_id,
      work_hours: this.museumData.work_hours
    });

    this.showSuccessAlert("Museum successfully added!");
    this.resetMuseumForm();
  } catch (error) {
    console.error("Error adding museum:", error);
    this.showErrorAlert("Failed to add museum");
  }
},

    async addRestaurant() {
      try {
        // 1. Vispirms izveidojam Object ierakstu
        const objectResponse = await axios.post(""http://localhost:3000/add_objects.php"", {
          name: this.restaurantData.name,
          description: this.restaurantData.description,
          address: this.restaurantData.address
        });
        
        // 2. Tad izveidojam Restaurant ierakstu
        const restaurantResponse = await axios.post("/api/add_restaurants.php", {
          objects_id: objectResponse.data.objects_id,
          avg_price: this.restaurantData.avg_price
        });
        
        this.showSuccessAlert("Restaurant successfully added!");
        this.resetRestaurantForm();
      } catch (error) {
        console.error("Error adding restaurant:", error);
        this.showErrorAlert("Failed to add restaurant");
      }
    },

    async addPlace() {
      try {
        // 1. Vispirms izveidojam Object ierakstu
        const objectResponse = await axios.post("/api/add_objects.php", {
          name: this.placeData.name,
          description: this.placeData.description,
          address: this.placeData.address
        });
        
        // 2. Tad izveidojam Place ierakstu
        const placeResponse = await axios.post("/api/add_places.php", {
          objects_id: objectResponse.data.objects_id,
          facts: this.placeData.facts
        });
        
        this.showSuccessAlert("Place successfully added!");
        this.resetPlaceForm();
      } catch (error) {
        console.error("Error adding place:", error);
        this.showErrorAlert("Failed to add place");
      }
    },

    // Palīgmetodes paliek nemainīgas
    showSuccessAlert(message) {
      alert(message);
    },
    showErrorAlert(message) {
      alert(message);
    },
    resetMuseumForm() {
      this.museumData = {
        name: "",
        description: "",
        address: "",
        work_hours: ""
      };
    },
    resetRestaurantForm() {
      this.restaurantData = {
        name: "",
        description: "",
        address: "",
        avg_price: null
      };
    },
    resetPlaceForm() {
      this.placeData = {
        name: "",
        description: "",
        address: "",
        facts: ""
      };
    }
  }
};
</script>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

.admin-dashboard {
  font-family: 'Poppins', sans-serif;
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  color: #333;
}

.dashboard-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.dashboard-header h2 {
  font-size: 2.2rem;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.dashboard-header .subtitle {
  color: #7f8c8d;
  font-size: 1.1rem;
}

.form-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
}

.form-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  padding: 1.8rem;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.form-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.form-card h3 {
  font-size: 1.4rem;
  margin-bottom: 1.5rem;
  color: #2c3e50;
  border-bottom: 2px solid #eee;
  padding-bottom: 0.8rem;
  display: flex;
  align-items: center;
  gap: 0.8rem;
}

.place-form {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 500;
  color: #555;
}

.form-input,
.form-textarea {
  padding: 0.8rem 1rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-family: 'Poppins', sans-serif;
  transition: border-color 0.3s ease;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}

.form-textarea {
  min-height: 100px;
  resize: vertical;
}

.submit-btn {
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.submit-btn i {
  font-size: 1.1rem;
}

.museum-btn {
  background-color: #3498db;
  color: white;
}

.museum-btn:hover {
  background-color: #2980b9;
}

.restaurant-btn {
  background-color: #e74c3c;
  color: white;
}

.restaurant-btn:hover {
  background-color: #c0392b;
}

.place-btn {
  background-color: #2ecc71;
  color: white;
}

.place-btn:hover {
  background-color: #27ae60;
}

@media (max-width: 768px) {
  .form-container {
    grid-template-columns: 1fr;
  }

  .dashboard-header h2 {
    font-size: 1.8rem;
  }
}
</style>
