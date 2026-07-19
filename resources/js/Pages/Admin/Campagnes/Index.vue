<!-- resources/js/Pages/Admin/Campagnes/Index.vue -->
<template>
  <AdminLayout title="Campagnes de collecte" description="Gérez les campagnes de collecte de données">
    <div class="space-y-6">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm font-medium text-slate-500">{{ campagnes.length }} campagne(s)</p>
        <Link href="/admin/campagnes/create" class="btn-primary">
          <PlusIcon class="h-4.5 w-4.5" />
          Nouvelle campagne
        </Link>
      </div>

      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="campagne in campagnes" :key="campagne.id" class="surface-card-hover overflow-hidden">
          <div class="relative h-36 bg-brand-100">
            <img v-if="campagne.image_url" :src="campagne.image_url" :alt="campagne.titre" class="h-full w-full object-cover" />
            <div v-else class="flex h-full w-full items-center justify-center bg-brand-gradient">
              <MegaphoneIcon class="h-9 w-9 text-white/80" />
            </div>
            <span class="absolute right-2.5 top-2.5 badge backdrop-blur-sm" :class="campagne.est_active ? 'bg-emerald-500/90 text-white' : 'bg-slate-500/80 text-white'">
              {{ campagne.est_active ? 'Active' : 'Inactive' }}
            </span>
          </div>

          <div class="p-4">
            <h3 class="mb-1.5 truncate font-bold text-slate-900">{{ campagne.titre }}</h3>
            <p class="mb-3 line-clamp-2 text-sm text-slate-500">{{ campagne.description }}</p>

            <div class="mb-3 flex flex-wrap gap-1.5">
              <span v-for="type in campagne.types_acceptes" :key="type" class="badge-brand">{{ typeLabels[type] }}</span>
            </div>

            <div v-if="campagne.projet" class="mb-3 flex items-center gap-1.5 text-xs text-slate-400">
              <LinkIcon class="h-3.5 w-3.5" />
              Lié au projet : {{ campagne.projet.titre }}
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 pt-3">
              <span class="text-xs font-medium text-slate-400">{{ campagne.contributions_count }} contribution(s)</span>
              <div class="flex items-center gap-1.5">
                <Link :href="`/admin/campagnes/${campagne.id}/edit`" class="btn-icon h-8 w-8" aria-label="Modifier">
                  <PencilIcon class="h-4 w-4" />
                </Link>
                <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg text-rose-600 transition-colors hover:bg-rose-50" aria-label="Supprimer" @click="confirmDelete(campagne)">
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="campagnes.length === 0" class="surface-card py-14 text-center">
        <MegaphoneIcon class="mx-auto mb-4 h-12 w-12 text-brand-200" />
        <p class="mb-1 font-medium text-slate-600">Aucune campagne de collecte</p>
        <p class="mb-4 text-sm text-slate-400">Créez-en une pour commencer à recevoir des contributions ciblées</p>
        <Link href="/admin/campagnes/create" class="btn-primary inline-flex">Créer votre première campagne</Link>
      </div>
    </div>

    <ConfirmationModal
      :show="showDeleteModal"
      variant="delete"
      title="Supprimer la campagne"
      message="Êtes-vous sûr de vouloir supprimer cette campagne ? Les contributions déjà reçues resteront enregistrées mais ne seront plus rattachées à une campagne active."
      :item-info="campagneToDelete?.titre"
      @confirm="deleteCampagne"
      @cancel="showDeleteModal = false"
    />
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ConfirmationModal from '@/Components/Admin/ConfirmationModal.vue'
import { LinkIcon, MegaphoneIcon, PencilIcon, PlusIcon, TrashIcon } from 'lucide-vue-next'

defineProps({
  campagnes: {
    type: Array,
    default: () => [],
  },
})

const typeLabels = {
  image: 'Images',
  texte: 'Textes',
  dataset: 'Datasets',
}

const showDeleteModal = ref(false)
const campagneToDelete = ref(null)

function confirmDelete(campagne) {
  campagneToDelete.value = campagne
  showDeleteModal.value = true
}

function deleteCampagne() {
  if (campagneToDelete.value) {
    router.delete(`/admin/campagnes/${campagneToDelete.value.id}`)
  }
  showDeleteModal.value = false
}
</script>
