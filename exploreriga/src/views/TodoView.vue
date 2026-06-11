<template>
  <div class="todo-page" :class="{ 'dark-mode': isDark }">

    <div class="todo-hero">
      <nav>
        <a href="/" class="brand">ExploreRiga</a>
        <div class="nav-links" id="todoNavLinks">
          <i class="fa fa-times" @click="hideMenu"></i>
          <ul>
            <li class="dropdown">
              <a href="#" class="dropbtn" @click.prevent="dropdownOpen = !dropdownOpen">{{ t('nav.aboutUs') }}</a>
              <div class="dropdown-content" :class="{ show: dropdownOpen }">
                <a href="/contacts">{{ t('nav.contacts') }}</a>
                <a href="/team">{{ t('nav.team') }}</a>
                <a href="/information">{{ t('nav.information') }}</a>
              </div>
            </li>
            <li><router-link to="/">{{ t('nav.home') }}</router-link></li>
            <li><router-link to="/museums">{{ t('nav.museums') }}</router-link></li>
            <li><router-link to="/restaurants">{{ t('nav.restaurants') }}</router-link></li>
            <li><router-link to="/culture">{{ t('nav.culture') }}</router-link></li>
            <li><router-link to="/todo">{{ t('nav.todo') }}</router-link></li>
            <li><button class="dark-mode-o" @click="toggleDarkMode"><i :class="isDark ? 'fa fa-sun' : 'fa fa-moon'"></i></button></li>
          </ul>
        </div>
        <div class="lang-switcher">
          <button v-for="l in ['lv','en','et','lt']" :key="l" @click="setLang(l)" :class="{ active: currentLang === l }">{{ l.toUpperCase() }}</button>
        </div>
        <i class="fa fa-bars" @click="showMenu"></i>
      </nav>

      <div class="hero-content">
        <h1>{{ t('todo.title') }}</h1>
        <p>{{ t('todo.heroSub') }}</p>
      </div>
    </div>

    <div class="todo-main">

      <div class="stats-row" v-if="tasks.length">
        <div class="stat-pill todo-pill">
          <span class="stat-num">{{ tasks.filter(t => t.status === 'to-do').length }}</span>
          <span class="stat-label">{{ t('todo.sTodo') }}</span>
        </div>
        <div class="stat-pill progress-pill">
          <span class="stat-num">{{ tasks.filter(t => t.status === 'in progress').length }}</span>
          <span class="stat-label">{{ t('todo.sInProgress') }}</span>
        </div>
        <div class="stat-pill done-pill">
          <span class="stat-num">{{ tasks.filter(t => t.status === 'finished').length }}</span>
          <span class="stat-label">{{ t('todo.sFinished') }}</span>
        </div>
      </div>

      <div class="input-card">
        <input
          v-model="task"
          type="text"
          class="task-input"
          :placeholder="t('todo.placeholder')"
          @keyup.enter="submitTask"
        />
        <button @click="submitTask" class="add-btn">
          <i :class="editedTask === null ? 'fa fa-plus' : 'fa fa-check'"></i>
          {{ editedTask === null ? t('todo.addBtn') : t('todo.updateBtn') }}
        </button>
      </div>

      <div v-if="tasks.length" class="task-list">
        <transition-group name="task">
          <div
            v-for="(item, index) in tasks"
            :key="index"
            class="task-card"
            :class="item.status === 'in progress' ? 'in-progress' : item.status"
          >
            <div class="task-left">
              <div class="status-dot" :class="item.status === 'in progress' ? 'in-progress' : item.status"></div>
              <span class="task-name" :class="{ finished: item.status === 'finished' }">{{ item.name }}</span>
            </div>
            <div class="task-right">
              <span
                class="status-badge"
                :class="item.status === 'in progress' ? 'in-progress' : item.status"
                @click="changeStatus(index)"
                title="Mainīt statusu"
              >{{ statusLabel(item.status) }}</span>
              <button class="icon-btn edit-btn" @click="editTask(index)" :title="t('todo.colEdit')">
                <i class="fa fa-pen"></i>
              </button>
              <button class="icon-btn delete-btn" @click="deleteTask(index)" :title="t('todo.colDelete')">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </div>
        </transition-group>
      </div>

      <div v-else class="empty-state">
        <div class="empty-icon">
          <i class="fa fa-clipboard-list"></i>
        </div>
        <h3>{{ t('todo.noTasks') }}</h3>
        <p>{{ t('todo.emptyHint') }}</p>
      </div>

    </div>

    <footer>
      <div class="footer-content">
        <p>&copy; 2024 ExploreRiga. Visas tiesības aizsargātas.</p>
      </div>
    </footer>
  </div>
</template>

<script>
import { useI18n } from '../i18n'
import { useDarkMode } from '@/composables/useDarkMode'

function getUser() {
  try { return JSON.parse(localStorage.getItem('user')) } catch { return null }
}

export default {
  name: 'TodoView',
  setup() {
    const { t, currentLang, setLang } = useI18n()
    const { isDark, toggleDarkMode } = useDarkMode()
    return { t, currentLang, setLang, isDark, toggleDarkMode }
  },
  data() {
    return {
      task: '',
      editedTask: null,
      dropdownOpen: false,
      availableStatuses: ['to-do', 'in progress', 'finished'],
      tasks: [],
      useLocalFallback: false,
    }
  },
  async mounted() {
    const user = getUser()
    if (user && user.id) {
      await this.loadTasksFromDB(user.id)
    } else {
      this.useLocalFallback = true
      const saved = localStorage.getItem('exploreriga-tasks')
      this.tasks = saved ? JSON.parse(saved) : [
        { name: 'Iesēsties lidmašīnā', status: 'to-do' },
        { name: 'Apmeklēt Rīgu', status: 'in progress' }
      ]
    }
  },
  methods: {
    showMenu() {
      const nav = document.getElementById('todoNavLinks')
      if (nav) nav.style.right = '0'
    },
    hideMenu() {
      const nav = document.getElementById('todoNavLinks')
      if (nav) nav.style.right = '-240px'
    },
    async loadTasksFromDB(userId) {
      try {
        const res = await fetch(`/api/get_tasks.php?user_id=${userId}`)
        const data = await res.json()
        if (data.tasks) this.tasks = data.tasks
      } catch (e) {
        console.error('Failed to load tasks:', e)
      }
    },
    async submitTask() {
      if (this.task.trim() === '') return

      if (this.editedTask !== null) {
        const item = this.tasks[this.editedTask]
        item.name = this.task.trim()
        this.editedTask = null
        this.task = ''
        if (this.useLocalFallback) {
          this.saveLocal()
        } else {
          const user = getUser()
          if (user && user.id) {
            await fetch('/api/update_task.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ id: item.id, name: item.name })
            })
          }
        }
        return
      }

      const newName = this.task.trim()
      this.task = ''

      if (this.useLocalFallback) {
        this.tasks.push({ name: newName, status: 'to-do' })
        this.saveLocal()
      } else {
        const user = getUser()
        if (!user || !user.id) return
        try {
          const res = await fetch('/api/add_task.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: user.id, name: newName })
          })
          const data = await res.json()
          if (data.success) this.tasks.push({ id: data.id, name: newName, status: 'to-do' })
        } catch (e) {
          console.error('Failed to add task:', e)
        }
      }
    },
    saveLocal() {
      localStorage.setItem('exploreriga-tasks', JSON.stringify(this.tasks))
    },
    async deleteTask(index) {
      const item = this.tasks[index]
      this.tasks.splice(index, 1)
      if (this.useLocalFallback) {
        this.saveLocal()
      } else if (item.id) {
        try {
          await fetch('/api/delete_task.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: item.id })
          })
        } catch (e) {
          console.error('Failed to delete task:', e)
        }
      }
    },
    editTask(index) {
      this.task = this.tasks[index].name
      this.editedTask = index
    },
    async changeStatus(index) {
      const item = this.tasks[index]
      const cur = this.availableStatuses.indexOf(item.status)
      item.status = this.availableStatuses[(cur + 1) % this.availableStatuses.length]
      if (this.useLocalFallback) {
        this.saveLocal()
      } else if (item.id) {
        try {
          await fetch('/api/update_task.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: item.id, status: item.status })
          })
        } catch (e) {
          console.error('Failed to update status:', e)
        }
      }
    },
    statusLabel(status) {
      const map = { 'to-do': 'todo.sTodo', 'in progress': 'todo.sInProgress', 'finished': 'todo.sFinished' }
      return this.t(map[status] || status)
    },
  }
}
</script>

<style scoped>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
h1, h2, h3 { font-family: 'Playfair Display', serif; }

/* ── Hero ── */
.todo-hero {
  width: 100%;
  min-height: 340px;
  background: linear-gradient(rgba(0,0,0,0.62), rgba(0,0,0,0.62)),
    url("https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=1600") center/cover;
  padding: 0 6%;
  display: flex;
  flex-direction: column;
}

/* ── Nav ── */
nav {
  display: flex;
  padding: 1.8% 0;
  align-items: center;
  gap: 12px;
}

.brand {
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: #fff;
  text-decoration: none;
  flex-shrink: 0;
}

.nav-links { flex: 1; text-align: right; }

.nav-links ul li {
  list-style: none;
  display: inline-block;
  padding: 8px 12px;
  position: relative;
}

.nav-links ul li a,
.nav-links ul li .router-link-active {
  color: #fff;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  letter-spacing: 0.8px;
  text-transform: uppercase;
}

.nav-links ul li::after {
  content: '';
  width: 0;
  height: 2px;
  background: #c847d8;
  display: block;
  margin: auto;
  transition: 0.4s;
}

.nav-links ul li:hover::after { width: 100%; }

nav .fa-bars { display: none; }
.nav-links .fa-times { display: none; }

.dropbtn {
  color: #fff;
  font-size: 13px;
  font-weight: 500;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px 12px;
}

.dropdown { position: relative; display: inline-block; }

.dropdown-content {
  display: none;
  position: absolute;
  background: #3b053d;
  min-width: 160px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  z-index: 10;
  border-radius: 8px;
  overflow: hidden;
  opacity: 0;
  transform: translateY(-8px);
  transition: opacity 0.25s, transform 0.25s;
}

.dropdown-content a {
  color: #fff;
  padding: 11px 16px;
  text-decoration: none;
  display: block;
  font-size: 13px;
}

.dropdown-content a:hover { background: rgba(255,255,255,0.1); }
.dropdown-content.show { display: block; opacity: 1; transform: translateY(0); }

.lang-switcher { display: flex; gap: 4px; }
.lang-switcher button {
  background: rgba(255,255,255,0.1);
  color: rgba(255,255,255,0.7);
  border: 1px solid rgba(255,255,255,0.2);
  padding: 5px 9px;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s;
}
.lang-switcher button:hover { background: rgba(255,255,255,0.2); color: #fff; }
.lang-switcher button.active { background: #991499; color: #fff; border-color: #991499; }

/* ── Hero text ── */
.hero-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 32px 0 48px;
}

.hero-content h1 {
  color: #fff;
  font-size: 64px;
  line-height: 1.1;
  margin-bottom: 16px;
}

.hero-content p {
  color: rgba(255,255,255,0.8);
  font-size: 18px;
  max-width: 480px;
}

/* ── Main ── */
.todo-main {
  max-width: 760px;
  margin: 0 auto;
  padding: 48px 24px 80px;
}

/* ── Stats ── */
.stats-row {
  display: flex;
  gap: 16px;
  margin-bottom: 32px;
  flex-wrap: wrap;
}

.stat-pill {
  flex: 1;
  min-width: 120px;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 18px 12px;
  border-radius: 14px;
  font-weight: 700;
}

.stat-pill.todo-pill    { background: #fff0f0; border: 1.5px solid #fca5a5; }
.stat-pill.progress-pill { background: #fffbeb; border: 1.5px solid #fcd34d; }
.stat-pill.done-pill    { background: #f0fdf4; border: 1.5px solid #86efac; }

.stat-num {
  font-size: 36px;
  font-family: 'Playfair Display', serif;
  line-height: 1;
  margin-bottom: 4px;
}

.todo-pill .stat-num    { color: #ef4444; }
.progress-pill .stat-num { color: #d97706; }
.done-pill .stat-num    { color: #16a34a; }

.stat-label {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: #666;
}

/* ── Input card ── */
.input-card {
  display: flex;
  gap: 12px;
  background: #fff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.09);
  margin-bottom: 32px;
}

.task-input {
  flex: 1;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  padding: 12px 18px;
  font-size: 15px;
  font-family: 'Inter', sans-serif;
  transition: border-color 0.2s;
  outline: none;
}

.task-input:focus { border-color: #991499; }

.add-btn {
  background: #991499;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 12px 24px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
  transition: background 0.25s, transform 0.2s;
}

.add-btn:hover { background: #6b0487; transform: translateY(-1px); }

/* ── Task list ── */
.task-list { display: flex; flex-direction: column; gap: 12px; }

.task-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  background: #fff;
  border-radius: 14px;
  padding: 18px 20px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.07);
  border-left: 4px solid transparent;
  transition: transform 0.25s, box-shadow 0.25s;
}

.task-card:hover { transform: translateX(4px); box-shadow: 0 6px 24px rgba(0,0,0,0.11); }

.task-card.to-do       { border-left-color: #ef4444; }
.task-card.in-progress { border-left-color: #f59e0b; }
.task-card.finished    { border-left-color: #22c55e; }

.task-left {
  display: flex;
  align-items: center;
  gap: 14px;
  flex: 1;
  min-width: 0;
}

.status-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
}

.status-dot.to-do       { background: #ef4444; }
.status-dot.in-progress { background: #f59e0b; }
.status-dot.finished    { background: #22c55e; }

.task-name {
  font-size: 15px;
  color: #1a1a2e;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.task-name.finished {
  text-decoration: line-through;
  color: #9ca3af;
}

.task-right {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

/* Status badge */
.status-badge {
  padding: 5px 14px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.5px;
  cursor: pointer;
  transition: opacity 0.2s, transform 0.15s;
  text-transform: uppercase;
  user-select: none;
}

.status-badge:hover { opacity: 0.8; transform: scale(0.96); }

.status-badge.to-do       { background: #fee2e2; color: #b91c1c; }
.status-badge.in-progress { background: #fef9c3; color: #92400e; }
.status-badge.finished    { background: #dcfce7; color: #15803d; }

/* Icon buttons */
.icon-btn {
  width: 34px;
  height: 34px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  transition: background 0.2s, transform 0.15s;
}

.icon-btn:hover { transform: scale(1.1); }

.edit-btn   { background: #eff6ff; color: #2563eb; }
.edit-btn:hover   { background: #dbeafe; }
.delete-btn { background: #fff1f2; color: #e11d48; }
.delete-btn:hover { background: #ffe4e6; }

/* ── Empty state ── */
.empty-state {
  text-align: center;
  padding: 64px 20px;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
}

.empty-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #f5d0fe, #e9d5ff);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  font-size: 32px;
  color: #991499;
}

.empty-state h3 {
  font-size: 22px;
  color: #1a1a2e;
  margin-bottom: 8px;
}

.empty-state p {
  color: #9ca3af;
  font-size: 15px;
}

/* ── Task transition ── */
.task-enter-active, .task-leave-active { transition: all 0.3s ease; }
.task-enter-from { opacity: 0; transform: translateY(-10px); }
.task-leave-to   { opacity: 0; transform: translateX(20px); }

/* ── Dark mode button ── */
.dark-mode-o {
  background: rgba(255,255,255,0.12); color: white; padding: 7px 14px;
  font-size: 14px; cursor: pointer; border: 1px solid rgba(255,255,255,0.25);
  border-radius: 8px; transition: background 0.3s;
}
.dark-mode-o:hover { background: rgba(255,255,255,0.22); }

/* ── Footer ── */
footer {
  background: linear-gradient(135deg, #1a001a 0%, #2d003a 100%);
  color: #fff;
  padding: 30px 5%;
  text-align: center;
}

.footer-content p { font-size: 13px; color: rgba(255,255,255,0.5); }

/* ── Dark mode ── */
.dark-mode { background-color: #121212; color: #fff; }
.dark-mode .todo-main { color: #fff; }
.dark-mode .input-card { background: #1e1e1e; box-shadow: 0 4px 24px rgba(0,0,0,0.4); }
.dark-mode .task-input { background: #2a2a2a; border-color: #3a3a3a; color: #fff; }
.dark-mode .task-input:focus { border-color: #991499; }
.dark-mode .task-input::placeholder { color: #666; }
.dark-mode .task-card { background: #1e1e1e; box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
.dark-mode .task-name { color: #f0e6ff; }
.dark-mode .task-name.finished { color: #555; }
.dark-mode .stat-pill.todo-pill { background: #2a1414; border-color: #7f1d1d; }
.dark-mode .stat-pill.progress-pill { background: #2a2410; border-color: #78350f; }
.dark-mode .stat-pill.done-pill { background: #0f2a18; border-color: #14532d; }
.dark-mode .stat-label { color: #aaa; }
.dark-mode .edit-btn { background: #1e2a3a; color: #60a5fa; }
.dark-mode .edit-btn:hover { background: #1e3a5f; }
.dark-mode .delete-btn { background: #2a1a1a; color: #f87171; }
.dark-mode .delete-btn:hover { background: #3a1a1a; }
.dark-mode .empty-state { background: #1e1e1e; box-shadow: 0 4px 24px rgba(0,0,0,0.4); }
.dark-mode .empty-state h3 { color: #f0e6ff; }
.dark-mode .empty-state p { color: #888; }
.dark-mode footer { background: linear-gradient(135deg, #0d0d0d, #1a001a); }

/* ── Mobile ── */
@media (max-width: 700px) {
  .hero-content h1 { font-size: 40px; }

  .nav-links {
    position: fixed;
    background: #3b053d;
    height: 100vh;
    width: 240px;
    top: 0;
    right: -240px;
    text-align: left;
    z-index: 1000;
    transition: 0.4s;
  }

  .nav-links ul li { display: block; }
  .nav-links ul { padding: 30px 20px; }

  nav .fa-bars {
    display: block;
    color: #fff;
    font-size: 22px;
    cursor: pointer;
  }

  .task-card { flex-wrap: wrap; }
  .input-card { flex-direction: column; }
  .add-btn { justify-content: center; }
}
</style>
