<template>
    <button @click="generateRoute">Test maršruts</button>
  <div>
    <div id="map" class="map"></div>

    <button @click="generateRoute" class="btn">
      🧭 Izveidot dienas maršrutu
    </button>
  </div>
</template>

<script>
import L from "leaflet";
import "leaflet/dist/leaflet.css";

export default {
  props: ["places"],

  data() {
    return {
      map: null,
      routeLine: null,
    };
  },

  mounted() {
    this.initMap();
  },

  methods: {
    initMap() {
      this.map = L.map("map").setView([56.9496, 24.1052], 13);

      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap",
      }).addTo(this.map);
    },

    generateRoute() {
      // 1. tikai vietas ar koordinātēm
      let points = this.places.filter(p => p.lat && p.lng);

      // 2. optimizē secību
      const ordered = this.sortByNearest(points);

      // 3. noņem veco līniju
      if (this.routeLine) {
        this.map.removeLayer(this.routeLine);
      }

      // 4. uztaisa līniju
      const latlngs = ordered.map(p => [p.lat, p.lng]);

      this.routeLine = L.polyline(latlngs, {
        color: "purple",
        weight: 4,
      }).addTo(this.map);

      // 5. zoom uz maršrutu
      this.map.fitBounds(this.routeLine.getBounds());

      // 6. opcija: Google Maps
      this.openGoogleRoute(ordered);
    },

    sortByNearest(points) {
      if (!points.length) return [];

      const result = [];
      let current = points[0];
      const remaining = points.slice(1);

      result.push(current);

      while (remaining.length) {
        let nearestIndex = 0;
        let nearestDist = Infinity;

        remaining.forEach((p, i) => {
          const d = this.getDistance(current, p);
          if (d < nearestDist) {
            nearestDist = d;
            nearestIndex = i;
          }
        });

        current = remaining.splice(nearestIndex, 1)[0];
        result.push(current);
      }

      return result;
    },

    getDistance(a, b) {
      const dx = a.lat - b.lat;
      const dy = a.lng - b.lng;
      return dx * dx + dy * dy;
    },

    openGoogleRoute(ordered) {
      const base = "https://www.google.com/maps/dir/";
      const waypoints = ordered
        .map(p => `${p.lat},${p.lng}`)
        .join("/");

      window.open(base + waypoints, "_blank");
    },
  },
};
</script>

<style scoped>
.map {
  height: 500px;
  border-radius: 12px;
  margin-bottom: 20px;
}

.btn {
  padding: 12px 18px;
  background: #991499;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}
</style>