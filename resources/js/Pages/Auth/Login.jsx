import ApplicationLogo from '@/Components/ApplicationLogo';
import InputError from '@/Components/InputError';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import GuestLayout from '@/Layouts/GuestLayout';
import { useForm } from '@inertiajs/react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const onHandleSubmit = (e) => {
        e.preventDefault();

        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <div className="w-full lg:mih-h-screen lg:grid lg:grid-cols-2">
            {/*Left Side Form */}
            <div className="flex flex-col px-6 py-4">
                <ApplicationLogo
                    bgLogo="from-blue-500 via-blue-600 to-blue-600"
                    colorLogo="text-white"
                    colorText="text-white"
                />

                <div className="flex flex-col items-center justify-center py-12 lg:py-48">
                    <div className="flex flex-col w-full gap-6 mx-auto lg:w-1/2">
                        <div className="grid gap-2 tex-center">
                            {status && (
                                <Alert variant="success">
                                    <AlertDescription>{status}</AlertDescription>
                                </Alert>
                            )}

                            <h1 className="text-3xl font-bold text-foreground">Masuk</h1>
                            <p className="text-balance text-muted-foreground">
                                Masukan email anda dibawah ini untuk masuk ke akun anda
                            </p>
                        </div>

                        <form onSubmit={onHandleSubmit}>
                            <div className="grid gap-4">
                                {/* form email */}
                                <div className="grid gap-2">
                                    <Label htmlFor="email">Email</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value={data.email}
                                        autoComplete="username"
                                        placeholder="luffy@siaku.test"
                                        // onChange={(e) => setData('email', e.target.value)}
                                        onChange={(e) => setData(e.target.name, e.target.value)}
                                    />

                                    {errors.email && <InputError message={errors.email} className="mt-2" />}
                                </div>

                                {/* form password */}
                                <div className="grid gap-2">
                                    <Label htmlFor="password">Password</Label>
                                    <Input
                                        id="password"
                                        type="password"
                                        name="password"
                                        value={data.password}
                                        autoComplete="new-password"
                                        placeholder="********"
                                        // onChange={(e) => setData('password', e.target.value)}
                                        onChange={(e) => setData(e.target.name, e.target.value)}
                                    />

                                    {errors.password && <InputError message={errors.password} className="mt-2" />}
                                </div>

                                {/* checkbox */}
                                <div className="grid gap-2">
                                    <div className="flex space-x-2 items-">
                                        <Checkbox
                                            id="remember"
                                            name="remember"
                                            checked={data.remember}
                                            onCheckedChange={(checked) => setData('remember', checked)}
                                        />

                                        <div className="grid gap-1.5 leading-none">
                                            <Label htmlFor="remember">Ingat Saya</Label>
                                        </div>
                                    </div>

                                    {errors.remember && <InputError message={errors.remember} className="mt-2" />}
                                </div>

                                <Button type="submit" disabled={processing} className="w-full" variant="blue" size="xl">
                                    Masuk
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {/* Right Side Image */}
            <div className="hidden bg-muted lg:block">
                <img
                    src="/images/bg-login.webp"
                    alt="Login Background"
                    className="object-cover w-full h-full max-h-screen"
                />
            </div>
        </div>
    );
}

Login.layout = (page) => <GuestLayout children={page} title="Login" />;
