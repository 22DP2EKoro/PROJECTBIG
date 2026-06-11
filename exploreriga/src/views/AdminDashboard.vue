<template>
  <div class="admin-page">

    <!-- Header -->
    <div class="admin-header">
      <a href="/" class="brand">ExploreRiga</a>
      <div class="header-center">
        <h1><i class="fa fa-shield-alt"></i> Administratora panelis</h1>
        <p>Pārvaldīt ExploreRiga saturu</p>
      </div>
      <button @click="handleSignOut" class="signout-btn">
        <i class="fa fa-sign-out-alt"></i> Iziet
      </button>
    </div>

    <!-- Stats Strip -->
    <div class="stats-strip">
      <div class="stat-pill" v-for="s in statsDisplay" :key="s.key">
        <i :class="s.icon"></i>
        <span class="stat-num">{{ entryCounts ? entryCounts[s.key] : '—' }}</span>
        <span class="stat-label">{{ s.label }}</span>
      </div>
      <button @click="fetchEntryCounts" class="refresh-btn" title="Atjaunināt skaitļus">
        <i class="fa fa-sync-alt"></i>
      </button>
    </div>

    <!-- Tab Bar -->
    <div class="tab-bar">
      <button :class="{ active: tab === 'add' }" @click="tab = 'add'">
        <i class="fa fa-plus-circle"></i> Pievienot
      </button>
      <button :class="{ active: tab === 'edit' }" @click="tab = 'edit'">
        <i class="fa fa-edit"></i> Rediģēt
      </button>
      <button :class="{ active: tab === 'delete' }" @click="tab = 'delete'">
        <i class="fa fa-trash-alt"></i> Dzēst
      </button>
      <button :class="{ active: tab === 'requests' }" @click="tab = 'requests'; fetchRequests()">
        <i class="fa fa-inbox"></i> Pieteikumi
        <span v-if="pendingCount" class="req-badge">{{ pendingCount }}</span>
      </button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">

      <!-- ADD -->
      <div v-if="tab === 'add'" class="panel">
        <div class="sub-tabs">
          <button :class="{ active: addTab === 'museum' }" @click="addTab = 'museum'">
            <i class="fa fa-landmark"></i> Muzejs
          </button>
          <button :class="{ active: addTab === 'restaurant' }" @click="addTab = 'restaurant'">
            <i class="fa fa-utensils"></i> Restorāns
          </button>
          <button :class="{ active: addTab === 'place' }" @click="addTab = 'place'">
            <i class="fa fa-map-marker-alt"></i> Vieta
          </button>
        </div>

        <form v-if="addTab === 'museum'" @submit.prevent="addMuseum" class="entry-form">
          <div class="form-row">
            <div class="form-group">
              <label>Muzeja nosaukums</label>
              <input type="text" v-model="museumData.name" required placeholder="Piem. Latvijas Nacionālais muzejs" />
            </div>
            <div class="form-group">
              <label>Darba laiks</label>
              <input type="text" v-model="museumData.work_hours" required placeholder="Piem. Pr–Pk 10:00–18:00" />
            </div>
          </div>
          <div class="form-group">
            <label>Apraksts</label>
            <textarea v-model="museumData.description" required placeholder="Aprakstiet muzeju..."></textarea>
          </div>
          <div class="form-group">
            <label>Adrese</label>
            <input type="text" v-model="museumData.address" required placeholder="Iela, Pilsēta" />
          </div>
          <button type="submit" class="action-btn add-btn">
            <i class="fa fa-plus-circle"></i> Pievienot muzeju
          </button>
        </form>

        <form v-if="addTab === 'restaurant'" @submit.prevent="addRestaurant" class="entry-form">
          <div class="form-row">
            <div class="form-group">
              <label>Restorāna nosaukums</label>
              <input type="text" v-model="restaurantData.name" required placeholder="Piem. Rīgas Restorāns" />
            </div>
            <div class="form-group">
              <label>Vidējā cena (€)</label>
              <input type="number" step="0.01" min="0" v-model="restaurantData.avg_price" required placeholder="Piem. 15.00" />
            </div>
          </div>
          <div class="form-group">
            <label>Apraksts</label>
            <textarea v-model="restaurantData.description" required placeholder="Aprakstiet restorānu..."></textarea>
          </div>
          <div class="form-group">
            <label>Adrese</label>
            <input type="text" v-model="restaurantData.address" required placeholder="Iela, Pilsēta" />
          </div>
          <button type="submit" class="action-btn add-btn">
            <i class="fa fa-plus-circle"></i> Pievienot restorānu
          </button>
        </form>

        <form v-if="addTab === 'place'" @submit.prevent="addPlace" class="entry-form">
          <div class="form-row">
            <div class="form-group">
              <label>Vietas nosaukums</label>
              <input type="text" v-model="placeData.name" required placeholder="Piem. Brīvības piemineklis" />
            </div>
            <div class="form-group">
              <label>Adrese</label>
              <input type="text" v-model="placeData.address" required placeholder="Iela, Pilsēta" />
            </div>
          </div>
          <div class="form-group">
            <label>Apraksts</label>
            <textarea v-model="placeData.description" required placeholder="Aprakstiet vietu..."></textarea>
          </div>
          <div class="form-group">
            <label>Fakti</label>
            <textarea v-model="placeData.facts" required placeholder="Interesanti fakti..."></textarea>
          </div>
          <button type="submit" class="action-btn add-btn">
            <i class="fa fa-plus-circle"></i> Pievienot vietu
          </button>
        </form>
      </div>

      <!-- EDIT -->
      <div v-if="tab === 'edit'" class="panel">
        <div class="load-row">
          <select v-model="editData.type" class="load-select">
            <option value="" disabled>Izvēlēties veidu</option>
            <option value="museum">Muzejs</option>
            <option value="restaurant">Restorāns</option>
            <option value="place">Vieta</option>
          </select>
          <input type="text" v-model="editData.name" class="load-input" placeholder="Ievadiet precīzu nosaukumu" />
          <button @click.prevent="loadEntry" class="action-btn load-btn">
            <i class="fa fa-search"></i> Ielādēt
          </button>
        </div>

        <form v-if="editFormVisible" @submit.prevent="submitEditEntry" class="entry-form edit-form">
          <div class="form-row">
            <div class="form-group">
              <label>Nosaukums</label>
              <input type="text" v-model="editData.name" required />
            </div>
            <div class="form-group">
              <label>Adrese</label>
              <input type="text" v-model="editData.address" required />
            </div>
          </div>
          <div class="form-group">
            <label>Apraksts</label>
            <textarea v-model="editData.description" required></textarea>
          </div>
          <div class="form-group" v-if="editData.type === 'museum'">
            <label>Darba laiks</label>
            <input type="text" v-model="editData.work_hours" />
          </div>
          <div class="form-group" v-if="editData.type === 'restaurant'">
            <label>Vidējā cena (€)</label>
            <input type="number" step="0.01" v-model="editData.avg_price" />
          </div>
          <div class="form-group" v-if="editData.type === 'place'">
            <label>Fakti</label>
            <textarea v-model="editData.facts"></textarea>
          </div>
          <button type="submit" class="action-btn save-btn">
            <i class="fa fa-save"></i> Saglabāt izmaiņas
          </button>
        </form>
      </div>

      <!-- DELETE -->
      <div v-if="tab === 'delete'" class="panel">
        <div class="delete-panel">
          <div class="warning-box">
            <i class="fa fa-exclamation-triangle"></i>
            Ieraksta dzēšana ir neatgriezeniska un nevar tikt atcelta.
          </div>
          <div class="load-row">
            <select v-model="deleteData.type" class="load-select">
              <option value="" disabled>Izvēlēties veidu</option>
              <option value="museum">Muzejs</option>
              <option value="restaurant">Restorāns</option>
              <option value="place">Vieta</option>
            </select>
            <input type="text" v-model="deleteData.name" class="load-input" placeholder="Ievadiet precīzu nosaukumu" />
            <button @click.prevent="deleteEntry" class="action-btn delete-btn">
              <i class="fa fa-trash-alt"></i> Dzēst
            </button>
          </div>
        </div>
      </div>

      <!-- REQUESTS -->
      <div v-if="tab === 'requests'" class="panel">
        <div class="requests-header">
          <h2><i class="fa fa-inbox"></i> Iesniegti pieteikumi</h2>
          <button @click="fetchRequests" class="action-btn load-btn" style="align-self:auto">
            <i class="fa fa-sync-alt"></i> Atjaunināt
          </button>
        </div>

        <div v-if="requestsLoading" class="req-loading">
          <i class="fa fa-spinner fa-spin"></i> Ielādē...
        </div>

        <div v-else-if="!placeRequests.length" class="req-empty">
          <i class="fa fa-inbox"></i>
          <p>Nav neviena pieteikuma</p>
        </div>

        <div v-else class="requests-list">
          <div v-for="req in placeRequests" :key="req.id" class="req-card" :class="'req-' + req.status">
            <div class="req-top">
              <div class="req-meta">
                <span class="req-status-badge" :class="req.status">{{ statusLabels[req.status] }}</span>
                <span class="req-cat"><i :class="catIcons[req.category]"></i> {{ req.category }}</span>
                <span class="req-date">{{ formatDate(req.submitted_at) }}</span>
              </div>
              <h3 class="req-name">{{ req.place_name }}</h3>
            </div>

            <div class="req-body">
              <div class="req-field" v-if="req.description">
                <strong>Apraksts:</strong> {{ req.description }}
              </div>
              <div class="req-row">
                <div class="req-field" v-if="req.address">
                  <strong><i class="fa fa-map-marker-alt"></i></strong> {{ req.address }}
                </div>
                <div class="req-field" v-if="req.opening_hours">
                  <strong><i class="fa fa-clock"></i></strong> {{ req.opening_hours }}
                </div>
              </div>
              <div class="req-row">
                <div class="req-field" v-if="req.website">
                  <strong><i class="fa fa-globe"></i></strong>
                  <a :href="req.website" target="_blank">{{ req.website }}</a>
                </div>
                <div class="req-field" v-if="req.image_url">
                  <strong><i class="fa fa-image"></i></strong>
                  <a :href="req.image_url" target="_blank">Attēls</a>
                </div>
              </div>
              <div class="req-contact" v-if="req.contact_name || req.contact_email">
                <strong>Kontakts:</strong>
                {{ req.contact_name }} <a :href="'mailto:' + req.contact_email">{{ req.contact_email }}</a>
              </div>
              <div class="req-field" v-if="req.notes">
                <strong>Piezīmes:</strong> {{ req.notes }}
              </div>
            </div>

            <div class="req-actions" v-if="req.status === 'pending'">
              <button class="action-btn approve-btn" @click="handleRequest(req.id, 'approved')">
                <i class="fa fa-check"></i> Apstiprināt
              </button>
              <button class="action-btn reject-btn" @click="handleRequest(req.id, 'rejected')">
                <i class="fa fa-times"></i> Noraidīt
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Toast -->
    <transition name="toast-slide">
      <div v-if="toast.show" class="toast" :class="'toast-' + toast.type">
        <i :class="toast.type === 'success' ? 'fa fa-check-circle' : 'fa fa-times-circle'"></i>
        {{ toast.message }}
      </div>
    </transition>

  </div>
</template>

<script>
import axios from 'axios'
import { signOut } from '../auth'
import { useRouter } from 'vue-router'

export default {
  setup() {
    const router = useRouter()
    return { router }
  },

  data() {
    return {
      tab: 'add',
      addTab: 'museum',
      museumData: { name: '', description: '', address: '', work_hours: '' },
      restaurantData: { name: '', description: '', address: '', avg_price: null },
      placeData: { name: '', description: '', address: '', facts: '' },
      editData: { type: '', name: '', description: '', address: '', work_hours: '', avg_price: null, facts: '', objects_id: null },
      deleteData: { type: '', name: '' },
      editFormVisible: false,
      entryCounts: null,
      placeRequests: [],
      requestsLoading: false,
      toast: { show: false, type: 'success', message: '' },
      statusLabels: { pending: 'Gaida', approved: 'Apstiprināts', rejected: 'Noraidīts' },
      catIcons: { museum: 'fa fa-landmark', restaurant: 'fa fa-utensils', culture: 'fa fa-theater-masks' },
      statsDisplay: [
        { key: 'museum',     label: 'Muzeji',       icon: 'fa fa-landmark' },
        { key: 'restaurant', label: 'Restorāni',    icon: 'fa fa-utensils' },
        { key: 'place',      label: 'Vietas',       icon: 'fa fa-map-marker-alt' },
        { key: 'user',       label: 'Lietotāji',    icon: 'fa fa-users' },
        { key: 'review',     label: 'Atsauksmes',   icon: 'fa fa-star' },
      ],
    }
  },

  mounted() {
    this.fetchEntryCounts()
    this.fetchRequests()
  },

  computed: {
    pendingCount() {
      return this.placeRequests.filter(r => r.status === 'pending').length
    },
  },

  methods: {
    showToast(type, message) {
      this.toast = { show: true, type, message }
      setTimeout(() => { this.toast.show = false }, 3500)
    },

    handleSignOut() {
      signOut()
      this.router.push('/')
    },

    async fetchEntryCounts() {
      try {
        const { data } = await axios.get('/api/get_entry_counts.php')
        this.entryCounts = data
      } catch (e) {
        console.error('Failed to fetch counts:', e)
      }
    },

    async addMuseum() {
      try {
        const { data } = await axios.post('/api/add_museum.php', this.museumData)
        if (data.error) { this.showToast('error', data.error); return }
        this.showToast('success', 'Muzejs veiksmīgi pievienots!')
        this.museumData = { name: '', description: '', address: '', work_hours: '' }
        this.fetchEntryCounts()
      } catch (e) {
        this.showToast('error', 'Neizdevās pievienot muzeju.')
      }
    },

    async addRestaurant() {
      try {
        const { data } = await axios.post('/api/add_restaurant.php', this.restaurantData)
        if (data.error) { this.showToast('error', data.error); return }
        this.showToast('success', 'Restorāns veiksmīgi pievienots!')
        this.restaurantData = { name: '', description: '', address: '', avg_price: null }
        this.fetchEntryCounts()
      } catch (e) {
        this.showToast('error', 'Neizdevās pievienot restorānu.')
      }
    },

    async addPlace() {
      try {
        const { data } = await axios.post('/api/add_place.php', this.placeData)
        if (data.error) { this.showToast('error', data.error); return }
        this.showToast('success', 'Vieta veiksmīgi pievienota!')
        this.placeData = { name: '', description: '', address: '', facts: '' }
        this.fetchEntryCounts()
      } catch (e) {
        this.showToast('error', 'Neizdevās pievienot vietu.')
      }
    },

    async loadEntry() {
      if (!this.editData.type || !this.editData.name.trim()) {
        this.showToast('error', 'Izvēlieties veidu un ievadiet nosaukumu.')
        return
      }
      const endpointMap = { museum: 'get_museum.php', restaurant: 'get_restaurant.php', place: 'get_place.php' }
      try {
        const { data } = await axios.post(
          `/api/${endpointMap[this.editData.type]}`,
          { name: this.editData.name.trim() }
        )
        if (data.error) { this.showToast('error', data.error); this.editFormVisible = false; return }
        const currentType = this.editData.type
        this.editData = { ...data, type: currentType }
        this.editFormVisible = true
        this.showToast('success', 'Ieraksts ielādēts.')
      } catch (e) {
        this.showToast('error', 'Neizdevās ielādēt ierakstu.')
      }
    },

    async submitEditEntry() {
      const endpointMap = { museum: 'update_museum.php', restaurant: 'update_restaurant.php', place: 'update_place.php' }
      try {
        const { data } = await axios.post(
          `/api/${endpointMap[this.editData.type]}`,
          this.editData
        )
        if (data.error) { this.showToast('error', data.error); return }
        this.showToast('success', 'Ieraksts veiksmīgi atjaunināts!')
        this.editFormVisible = false
      } catch (e) {
        this.showToast('error', 'Neizdevās atjaunināt ierakstu.')
      }
    },

    async fetchRequests() {
      this.requestsLoading = true
      try {
        const { data } = await axios.get('/api/get_place_requests.php')
        this.placeRequests = Array.isArray(data) ? data : []
      } catch (e) {
        console.error('Failed to fetch requests:', e)
      } finally {
        this.requestsLoading = false
      }
    },

    async handleRequest(id, action) {
      try {
        const { data } = await axios.post('/api/handle_place_request.php', { id, action })
        if (data.error) { this.showToast('error', data.error); return }
        const req = this.placeRequests.find(r => r.id === id)
        if (req) req.status = action
        this.showToast('success', action === 'approved' ? 'Pieteikums apstiprināts!' : 'Pieteikums noraidīts.')
      } catch {
        this.showToast('error', 'Neizdevās apstrādāt pieteikumu.')
      }
    },

    formatDate(ts) {
      if (!ts) return ''
      return new Date(ts).toLocaleDateString('lv-LV', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
    },

    async deleteEntry() {
      if (!this.deleteData.type || !this.deleteData.name.trim()) {
        this.showToast('error', 'Izvēlieties veidu un ievadiet nosaukumu.')
        return
      }
      const confirmed = window.confirm(`Dzēst "${this.deleteData.name}"? Šo darbību nevar atcelt.`)
      if (!confirmed) return
      const endpointMap = { museum: 'delete_museum.php', restaurant: 'delete_restaurant.php', place: 'delete_place.php' }
      try {
        const { data } = await axios.post(
          `/api/${endpointMap[this.deleteData.type]}`,
          { name: this.deleteData.name.trim() }
        )
        if (data.error) { this.showToast('error', data.error); return }
        this.showToast('success', 'Ieraksts dzēsts.')
        this.deleteData = { type: '', name: '' }
        this.fetchEntryCounts()
      } catch (e) {
        this.showToast('error', 'Neizdevās dzēst ierakstu.')
      }
    },
  },
}
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }

.admin-page { background: #f4f1f8; min-height: 100vh; }

/* Header */
.admin-header {
  background: linear-gradient(135deg, #3b053d 0%, #6b0487 100%);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 40px;
  gap: 16px;
}
.brand {
  font-family: 'Playfair Display', serif;
  font-size: 1.3rem;
  font-weight: 700;
  color: rgba(255,255,255,0.85);
  text-decoration: none;
  white-space: nowrap;
}
.brand:hover { color: #fff; }
.header-center { text-align: center; flex: 1; }
.header-center h1 { font-size: 1.8rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 10px; }
.header-center p { font-size: 0.9rem; color: rgba(255,255,255,0.65); margin-top: 4px; }
.signout-btn {
  background: rgba(255,255,255,0.15);
  color: #fff;
  border: 1px solid rgba(255,255,255,0.3);
  padding: 8px 18px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  white-space: nowrap;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}
.signout-btn:hover { background: rgba(255,255,255,0.28); }

/* Stats */
.stats-strip {
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 16px 40px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  flex-wrap: wrap;
}
.stat-pill {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f4f1f8;
  border-radius: 50px;
  padding: 8px 20px;
}
.stat-pill .fa { color: #6b0487; font-size: 0.95rem; }
.stat-num { font-size: 1.4rem; font-weight: 700; color: #3b053d; }
.stat-label { font-size: 0.8rem; color: #777; text-transform: uppercase; letter-spacing: 0.5px; }
.refresh-btn {
  background: #6b0487;
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background 0.2s;
}
.refresh-btn:hover { background: #3b053d; }

/* Tab Bar */
.tab-bar {
  display: flex;
  justify-content: center;
  gap: 8px;
  padding: 24px 40px 0;
}
.tab-bar button {
  background: #fff;
  border: 2px solid #e0d4e8;
  color: #555;
  padding: 10px 28px;
  border-radius: 50px;
  cursor: pointer;
  font-size: 0.95rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}
.tab-bar button:hover { border-color: #6b0487; color: #6b0487; }
.tab-bar button.active { background: #6b0487; border-color: #6b0487; color: #fff; }

/* Tab Content */
.tab-content { max-width: 860px; margin: 24px auto 64px; padding: 0 24px; }
.panel { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.07); padding: 32px; }

/* Sub Tabs */
.sub-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 28px;
  border-bottom: 2px solid #f0e8f4;
  padding-bottom: 16px;
}
.sub-tabs button {
  background: none;
  border: none;
  color: #888;
  padding: 8px 18px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s;
}
.sub-tabs button:hover { background: #f4f1f8; color: #6b0487; }
.sub-tabs button.active { background: #f4f1f8; color: #6b0487; font-weight: 600; }

/* Forms */
.entry-form { display: flex; flex-direction: column; gap: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 0.85rem; font-weight: 600; color: #555; text-transform: uppercase; letter-spacing: 0.4px; }
.form-group input,
.form-group textarea,
.form-group select {
  padding: 11px 14px;
  border: 1.5px solid #e0d4e8;
  border-radius: 10px;
  font-size: 0.95rem;
  color: #333;
  transition: border-color 0.2s, box-shadow 0.2s;
  background: #fdfbfe;
}
.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #6b0487;
  box-shadow: 0 0 0 3px rgba(107,4,135,0.1);
}
.form-group textarea { min-height: 96px; resize: vertical; }

/* Load Row */
.load-row {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 24px;
}
.load-select {
  padding: 11px 14px;
  border: 1.5px solid #e0d4e8;
  border-radius: 10px;
  font-size: 0.95rem;
  background: #fdfbfe;
  cursor: pointer;
  min-width: 160px;
}
.load-input {
  flex: 1;
  padding: 11px 14px;
  border: 1.5px solid #e0d4e8;
  border-radius: 10px;
  font-size: 0.95rem;
  background: #fdfbfe;
  min-width: 200px;
}
.load-select:focus, .load-input:focus {
  outline: none;
  border-color: #6b0487;
  box-shadow: 0 0 0 3px rgba(107,4,135,0.1);
}
.edit-form { border-top: 2px solid #f0e8f4; padding-top: 24px; }

/* Action Buttons */
.action-btn {
  padding: 12px 28px;
  border: none;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
  align-self: flex-start;
}
.add-btn  { background: #6b0487; color: #fff; }
.add-btn:hover  { background: #3b053d; }
.load-btn { background: #3498db; color: #fff; }
.load-btn:hover { background: #2980b9; }
.save-btn { background: #27ae60; color: #fff; }
.save-btn:hover { background: #1e8449; }
.delete-btn { background: #e74c3c; color: #fff; }
.delete-btn:hover { background: #c0392b; }

/* Delete panel */
.delete-panel { display: flex; flex-direction: column; gap: 20px; }
.warning-box {
  background: #fff5f5;
  border: 1.5px solid #f5c6c6;
  color: #c0392b;
  padding: 12px 18px;
  border-radius: 10px;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 10px;
}

/* Requests tab */
.req-badge {
  background: #e74c3c;
  color: #fff;
  border-radius: 50px;
  padding: 2px 7px;
  font-size: 0.75rem;
  font-weight: 700;
  margin-left: 4px;
}

.requests-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}

.requests-header h2 {
  font-size: 1.3rem;
  color: #3b053d;
  display: flex;
  align-items: center;
  gap: 10px;
}

.req-loading, .req-empty {
  text-align: center;
  padding: 48px;
  color: #aaa;
  font-size: 1rem;
}

.req-empty .fa { font-size: 2.5rem; display: block; margin-bottom: 12px; }

.requests-list { display: flex; flex-direction: column; gap: 16px; }

.req-card {
  border: 1.5px solid #e0d4e8;
  border-radius: 14px;
  overflow: hidden;
  background: #fdfbfe;
  transition: box-shadow 0.2s;
}

.req-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
.req-card.req-approved { border-color: #86efac; background: #f0fdf4; }
.req-card.req-rejected { border-color: #fca5a5; background: #fff5f5; opacity: 0.7; }

.req-top {
  padding: 16px 20px 12px;
  border-bottom: 1px solid #f0e8f4;
  background: #fff;
}

.req-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
  flex-wrap: wrap;
}

.req-status-badge {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 3px 10px;
  border-radius: 50px;
}

.req-status-badge.pending  { background: #fef9c3; color: #92400e; }
.req-status-badge.approved { background: #dcfce7; color: #15803d; }
.req-status-badge.rejected { background: #fee2e2; color: #b91c1c; }

.req-cat { font-size: 12px; color: #777; display: flex; align-items: center; gap: 5px; }
.req-cat .fa { color: #6b0487; }
.req-date { font-size: 11px; color: #aaa; margin-left: auto; }

.req-name { font-size: 1.15rem; font-weight: 700; color: #1a1a2e; font-family: 'Playfair Display', serif; }

.req-body {
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.req-row { display: flex; gap: 20px; flex-wrap: wrap; }
.req-field { font-size: 13px; color: #555; line-height: 1.5; flex: 1; min-width: 160px; }
.req-field a { color: #6b0487; text-decoration: none; }
.req-field a:hover { text-decoration: underline; }
.req-contact { font-size: 13px; color: #555; }
.req-contact a { color: #6b0487; text-decoration: none; }

.req-actions {
  display: flex;
  gap: 10px;
  padding: 12px 20px 16px;
  border-top: 1px solid #f0e8f4;
}

.approve-btn { background: #27ae60; color: #fff; }
.approve-btn:hover { background: #1e8449; }
.reject-btn  { background: #e74c3c; color: #fff; }
.reject-btn:hover  { background: #c0392b; }

/* Toast */
.toast {
  position: fixed;
  bottom: 32px;
  right: 32px;
  padding: 14px 24px;
  border-radius: 12px;
  font-size: 0.95rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.18);
  z-index: 9999;
}
.toast-success { background: #27ae60; color: #fff; }
.toast-error   { background: #e74c3c; color: #fff; }
.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateY(20px); }

@media (max-width: 600px) {
  .admin-header { flex-direction: column; text-align: center; padding: 20px; }
  .form-row { grid-template-columns: 1fr; }
  .stats-strip { gap: 8px; padding: 12px 16px; }
  .tab-bar { gap: 4px; padding: 16px 16px 0; }
  .tab-bar button { padding: 8px 16px; font-size: 0.85rem; }
}
</style>
