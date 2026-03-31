categories=>BOXING
activities=centers=>mwaytay, Monday : 10AM-11AM

ممتاز 👌 واضح إنك وصلت لمرحلة متقدمة في الـ database design لمشروعك.
رح أعطيك **notes بسيطة وواضحة لكل table**: شو دورها، وكيف بتتصل بباقي الجداول، وكيف المشروع بيمشي من خلالها خطوة خطوة.

---

# الفكرة العامة قبل التفاصيل

المشروع تبعك هو **منصة حجز أنشطة**.
يعني السيناريو:

1. User يسجل
2. Center owner ينشئ مركز
3. المركز يضيف Activities
4. النشاط عنده Schedule
5. المستخدم يعمل Review أو Favourite

هيدا هو flow الأساسي.

---

# 1) users table

## شو دورها

تمثل كل الأشخاص في النظام.

## الحقول المهمة

* `id` → رقم المستخدم
* `email` → الإيميل
* `phone` → رقم الهاتف
* `role` → نوع المستخدم

## role values

```text
user
center_owner
admin
```

## مثال بيانات

| id | name  | role         |
| -- | ----- | ------------ |
| 1  | Omar  | user         |
| 2  | Ali   | center_owner |
| 3  | Admin | admin        |

---

# 2) centers table

## شو دورها

تمثل **المراكز** (gym، karate club، music school...)

## العلاقة

```text
User (center_owner)
        ↓
      Center
```

يعني:

User واحد
يقدر يكون عنده
عدة Centers

## أهم الحقول

* `user_id` → صاحب المركز
* `name` → اسم المركز
* `address` → العنوان
* `city` → المدينة
* `lat / lng` → location على الخريطة
* `is_active` → مفعل أو لا

## مثال

| id | user_id | name      |
| -- | ------- | --------- |
| 1  | 2       | Tiger Gym |

---

# 3) categories table

## شو دورها

تمثل أنواع الأنشطة.

مثل:

* Martial Arts
* Swimming
* Football
* Arts

## مثال

| id | name         |
| -- | ------------ |
| 1  | Martial Arts |
| 2  | Swimming     |

## العلاقة

```text
Category
     ↓
 Activities
```

Category واحدة
عندها
عدة Activities

---

# 4) activities table

## أهم جدول في المشروع

يمثل النشاط نفسه.

مثل:

* Karate for kids
* Swimming class
* Yoga session

## العلاقات

```text
Center
   ↓
 Activities
   ↓
Category
```

يعني:

Activity
تنتمي إلى:

* Center
* Category

---

## أهم الحقول

* `center_id`
* `category_id`
* `title`
* `price`
* `capacity`
* `level`
* `is_private`

---

## مثال

| id | title           | price |
| -- | --------------- | ----- |
| 1  | Karate for kids | 50    |

---

# 5) schedules table

## شو دورها

تحدد **مواعيد النشاط**.

يعني:

متى يبدأ
ومتى ينتهي
وفي أي يوم

---

## العلاقة

```text
Activity
   ↓
 Schedules
```

Activity واحدة
عندها
عدة Schedules

---

## مثال

| activity_id | day       | start |
| ----------- | --------- | ----- |
| 1           | monday    | 16:00 |
| 1           | wednesday | 17:00 |

---

# 6) reviews table

## شو دورها

تمثل تقييم المستخدم للنشاط.

---

## العلاقة

```text
User
   ↓
 Review
   ↓
 Activity
```

يعني:

User
يقيم
Activity

---

## constraint مهم

```php
$table->unique(['user_id', 'activity_id']);
```

يعني:

المستخدم
يقدر يعمل
Review واحد فقط
لكل Activity

---

## مثال

| user | activity | rating |
| ---- | -------- | ------ |
| Omar | Karate   | 5      |

---

# 7) favourites table

## شو دورها

تمثل الأنشطة المفضلة للمستخدم.

---

## العلاقة

```text
User
   ↓
 Favourite
   ↓
 Activity
```

---

## مثال

| user | activity |
| ---- | -------- |
| Omar | Swimming |

---

# 8) schedules table (مكرر عندك)

أنت عندك:

```text
schedules
```

مرتين.

لازم يبقى:

```text
migration واحد فقط
```

---

# العلاقات الكاملة للمشروع

```text
User
  ├── Centers
  │       └── Activities
  │               ├── Category
  │               ├── Schedules
  │               ├── Reviews
  │               └── Favourites
```

---

# كيف المشروع يمشي خطوة خطوة

## 1) تسجيل مستخدم

يتم إنشاء:

```text
users
```

---

## 2) إنشاء مركز

يتم إنشاء:

```text
centers
```

---

## 3) إضافة Activity

يتم إنشاء:

```text
activities
```

---

## 4) إضافة Schedule

يتم إنشاء:

```text
schedules
```

---

## 5) المستخدم يشوف النشاط

يتم قراءة:

```text
activities
categories
centers
schedules
reviews
```

---

## 6) المستخدم يعمل Favourite

يتم إنشاء:

```text
favourites
```

---

## 7) المستخدم يعمل Review

يتم إنشاء:

```text
reviews
```

---

# ملاحظات تقنية مهمة

## 1) schedules مكرر

احذف واحد.

---

## 2) rating لازم constraint

يفضل:

```php
$table->unsignedTinyInteger('rating')
      ->check('rating >= 1 AND rating <= 5');
```

---

## 3) activities level

ممكن تعملها enum:

```php
$table->enum('level', [
    'beginner',
    'intermediate',
    'advanced'
]);
```

---

## 4) city

ممكن لاحقاً تعمل:

```text
cities table
```

لكن حالياً OK.

---

# هل الديزاين تبعك جيد؟

الإجابة:

نعم — **design احترافي لمشروع MVP**
وفيه:

* normalization صحيح
* relationships واضحة
* scalable
* production-ready تقريبًا

---

