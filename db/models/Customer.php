<?php

require_once __DIR__ . "/../Model.php";

class Customer extends Model
{
    protected $table = "customers";

    public int $id;
    public string $fname;
    public string $lname;
    public string $email;
    public string $password;
    public string $phone;
    public null | array| string $avatar;
    public DateTime $created_at;
    public DateTime $updated_at;
    public int $onboarded;
    public int $membership_plan;
    public DateTime|null $membership_plan_activated_at;

    public function fill(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->fname = $data['fname'] ?? "";
        $this->lname = $data['lname'] ?? "";
        $this->email = $data['email'] ?? "";
        $this->password = $data['password'] ?? "";
        $this->phone = $data['phone'] ?? "";
        $this->avatar = $data['avatar'] ?? null;
        $this->created_at = new DateTime($data['created_at'] ?? null);
        $this->updated_at = new DateTime($data['updated_at'] ?? $data['created_at'] ?? null);
        $this->updated_at = new DateTime($data['updated_at'] ?? $data['created_at'] ?? null);
        $this->membership_plan_activated_at = array_key_exists('membership_plan_activated_at', $data) ?  new DateTime($data['membership_plan_activated_at']) : null;
        $this->onboarded = $data['onboarded'] ?? 0;
        $this->membership_plan = $data['membership_plan'] ?? 0;
    }

    public function create()
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO $this->table (fname,lname, email, phone, avatar,password, onboarded,membership_plan) VALUES (:fname, :lname, :email, :phone, :avatar, :password, :onboarded, :membership_plan)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'fname' => $this->fname,
            'lname' => $this->lname,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'password' => $this->password,
            'onboarded' => $this->onboarded,
            'membership_plan' => $this->membership_plan,
        ]);
        $this->id = $this->conn->lastInsertId();
    }


    public function update()
    {
        $sql = "UPDATE $this->table SET fname=:fname, lname=:lname, email=:email, phone=:phone, avatar=:avatar, password=:password, onboarded=:onboarded, membership_plan=:membership_plan, updated_at=:updated_at,membership_plan_activated_at=:membership_plan_activated_at WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'id' => $this->id,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'password' => $this->password,
            'onboarded' => $this->onboarded,
            'membership_plan' => $this->membership_plan,
            'updated_at' => $this->updated_at->format("Y-m-d H:i:s"),
            'membership_plan_activated_at' => $this->membership_plan_activated_at ? $this->membership_plan_activated_at->format("Y-m-d H:i:s") : null
        ]);
    }

    public function save()
    {
        if ($this->id === 0) {
            $this->create();
        } else {
            $this->update();
        }
    }

    public function get_by_email()
    {
        $sql = "SELECT * FROM $this->table WHERE email=:email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['email' => $this->email]);
        $data = $stmt->fetch();
        if ($data) {
            $this->fill($data);
        }
    }

    public function get_by_id()
    {
        $sql = "SELECT * FROM $this->table WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $this->id]);
        $data = $stmt->fetch();
        if ($data) {
            $this->fill($data);
        }
    }

    public function get_all_by_membership_plan_id(string $membership_plan_id)
    {
        $sql = "SELECT * FROM $this->table WHERE membership_plan=:membership_plan_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['membership_plan_id' => $membership_plan_id]);
        $data = $stmt->fetchAll();
        $users = array_map(function ($user) {
            $new_user = new Customer();
            $new_user->fill($user);
            return $new_user;
        }, $data);
        return $users;
    }

    public function update_password()
    {
        $field = $this->id ? 'id' : ($this->email ? "email" : null);
        if (!$field) {
            throw new PDOException("Id or email is required to update password");
        }
        $sql = "UPDATE $this->table SET password=:password, updated_at=CURRENT_TIMESTAMP WHERE $field=:$field";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $field => $this->$field,
            'password' => $this->password
        ]);
    }
}
