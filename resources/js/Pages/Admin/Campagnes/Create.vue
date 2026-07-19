<!-- resources/js/Pages/Admin/Campagnes/Create.vue -->
<template>
  <AdminLayout title="Créer une campagne" description="Configurez une nouvelle campagne de collecte de données">
    <div class="mx-auto max-w-3xl">
      <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900">Nouvelle campagne</h1>
        <Link href="/admin/campagnes" class="flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-700">
          <ArrowLeftIcon class="h-4 w-4" />
          Retour à la liste
        </Link>
      </div>

      <form @submit.prevent="submit" class="surface-card space-y-6 p-6">
        <div>
          <label class="field-label">Titre *</label>
          <input v-model="form.titre" type="text" required class="field-input" placeholder="ex : Dataset agriculture Cameroun" />
          <p v-if="form.errors.titre" class="field-error">{{ form.errors.titre }}</p>
        </div>

        <div>
          <label class="field-label">Description *</label>
          <textarea v-model="form.description" rows="4" required class="field-input" placeholder="Expliquez aux contributeurs ce que vous recherchez et pourquoi" />
          <p class="field-hint">Ce texte sera visible par les visiteurs sur la page de collecte.</p>
          <p v-if="form.errors.description" class="field-error">{{ form.errors.description }}</p>
        </div>

        <div>
          <label class="field-label">Types de contribution acceptés *</label>
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <label
              v-for="type in typesDisponibles"
              :key="type.value"
              class="flex cursor-pointer items-center gap-3 rounded-xl border px-4 py-3 transition-colors"
              :class="form.types_acceptes.includes(type.value) ? 'border-brand-400 bg-brand-50' : 'border-slate-200 bg-white hover:bg-slate-50'"
            >
              <input type="checkbox" :value="type.value" v-model="form.types_acceptes" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-400" />
              <component :is="type.icon" class="h-4.5 w-4.5 text-brand-600" />
              <span class="text-sm font-medium text-slate-700">{{ type.label }}</span>
            </label>
          </div>
          <p class="field-hint">Seuls les formulaires correspondants seront proposés aux contributeurs de cette campagne.</p>
          <p v-if="form.errors.types_acceptes" class="field-error">{{ form.errors.types_acceptes }}</p>
        </div>

        <div>
          <label class="field-label">Projet lié (optionnel)</label>
          <select v-model="form.projet_id" class="field-input">
            <option :value="null">Aucun projet lié</option>
            <option v-for="projet in projets" :key="projet.id" :value="projet.id">{{ projet.titre }}</option>
          </select>
          <p class="field-hint">Si renseigné, un bouton "Contribuer" apparaîtra sur la fiche de ce projet dans "Nos réalisations".</p>
        </div>

        <ImageUploader
          label="Image de couverture (optionnel)"
          :model-url="form.image_url"
          @update:file="(file) => (form.image = file)"
          @update:url="(url) => (form.image_url = url)"
        />
        <p v-if="form.errors.image || form.errors.image_url" class="field-error -mt-3">{{ form.errors.image || form.errors.image_url }}</p>

        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-brand-100 bg-brand-50/50 px-4 py-3">
          <input v-model="form.est_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-400" />
          <span class="text-sm font-medium text-slate-700">Campagne active (visible et ouverte aux contributions)</span>
        </label>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-6">
          <Link href="/admin/campagnes" class="btn-secondary">Annuler</Link>
          <button type="submit" class="btn-primary" :disabled="form.processing">
            {{ form.processing ? 'Création…' : 'Créer la campagne' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ImageUploader from '@/Components/Admin/ImageUploader.vue'
import { ArrowLeftIcon, DatabaseIcon, ImageIcon, TypeIcon } from 'lucide-vue-next'

defineProps({
  projets: {
    type: Array,
    default: () => [],
  },
})

const typesDisponibles = [
  { value: 'image', label: 'Images', icon: ImageIcon },
  { value: 'texte', label: 'Textes', icon: TypeIcon },
  { value: 'dataset', label: 'Datasets', icon: DatabaseIcon },
]

const form = useForm({
  titre: '',
  description: '',
  types_acceptes: [],
  projet_id: null,
  image: null,
  image_url: '',
  est_active: true,
})

const submit = () => {
  form.post('/admin/campagnes', { forceFormData: true })
}
</script>
