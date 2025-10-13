# Structure des entités Blog

## ✅ Traits créés

### BlogStatusTrait
**Fichiers** :
- `src/Enum/BlogStatusEnum.php`
- `src/Entity/Traits/BlogStatusTrait.php`

**Statuts disponibles** :
- `DRAFT` (brouillon) - Gris, icône file-edit
- `PUBLISHED` (publié) - Vert, icône check-circle
- `PRIVATE` (privé) - Orange, icône lock
- `PREMIUM` (premium) - Violet, icône crown

**Propriétés** :
```php
private ?BlogStatusEnum $status = null;
private int $viewCount = 0;
private int $likeCount = 0;
private int $commentCount = 0;
```

**Méthodes helper** :
```php
isPublished(): bool
isDraft(): bool
isPrivate(): bool
isPremium(): bool
getStatusLabel(): string
getStatusColor(): string
getStatusIcon(): string
```

**Méthodes statistiques** :
```php
incrementViewCount()
incrementLikeCount()
decrementLikeCount()
incrementCommentCount()
decrementCommentCount()
isPopular(): bool (>= 100 vues)
hasInteractions(): bool
getEngagementScore(): int
```

## 📋 Relations à définir

### Questions en attente :

#### 1. BlgArticle (articles de blog)
- [ ] Relation avec BlgCategory ? (ManyToOne, ManyToMany ?)
- [ ] Relation avec BlgSection ? (ManyToOne, ManyToMany ?)
- [ ] Relation avec BlgTag ? (ManyToMany ?)
- [ ] Relation avec BlgComment ? (OneToMany ?)
- [ ] Relation avec BlgLike ? (OneToMany ?)

#### 2. BlgPage (pages statiques)
- [ ] Relation avec BlgCategory ? (ManyToOne, ManyToMany ?)
- [ ] Relation avec BlgSection ? (ManyToOne, ManyToMany ?)
- [ ] Relation avec BlgTag ? (ManyToMany ?)
- [ ] Relation avec BlgComment ? (OneToMany ?)
- [ ] Relation avec BlgLike ? (OneToMany ?)

#### 3. BlgComment (commentaires)
- [ ] Sur quoi peut-on commenter ? (BlgArticle, BlgPage, ou les deux ?)
- [ ] Les commentaires peuvent avoir des réponses (parent/enfant) ?

#### 4. BlgLike (likes)
- [ ] Sur quoi peut-on liker ? (BlgArticle, BlgPage, BlgComment ?)

#### 5. BlgCategory et BlgSection (hiérarchie)
- [ ] Relation self-referencing (parent/children) ?
- [ ] Quelle est la différence d'usage entre Category et Section ?

#### 6. BlgTag (tags)
- [ ] Les tags sont liés à quoi ? (BlgArticle, BlgPage, ou les deux ?)

## 🎯 Traits à appliquer

### BlgArticle
- ✅ Site + Author (déjà fait)
- [ ] TimeTrait
- [ ] TitleTrait
- [ ] ContentTrait
- [ ] SeoTrait
- [ ] BlogStatusTrait

### BlgPage
- ✅ Site + Author (déjà fait)
- [ ] TimeTrait
- [ ] TitleTrait
- [ ] ContentTrait
- [ ] SeoTrait
- [ ] BlogStatusTrait

### BlgCategory
- ✅ Site (déjà fait)
- [ ] TitleTrait
- [ ] SeoTrait
- [ ] ParentTrait (hiérarchie)
- [ ] BlogStatusTrait

### BlgSection
- ✅ Site (déjà fait)
- [ ] TitleTrait
- [ ] SeoTrait
- [ ] ParentTrait (hiérarchie)
- [ ] BlogStatusTrait

### BlgComment
- ✅ Site + Author (déjà fait)
- [ ] TimeTrait
- [ ] ContentTrait
- [ ] ParentTrait (si réponses)

### BlgTag
- ✅ Site (déjà fait)
- [ ] TitleTrait
- [ ] SeoTrait

### BlgLike
- ✅ Site + User (déjà fait)
- [ ] TimeTrait

## 📝 Prochaines étapes

1. Définir toutes les relations entre entités
2. Appliquer les traits à chaque entité
3. Générer les migrations
4. Créer les repositories
5. Créer les services (managers)
