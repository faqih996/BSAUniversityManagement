import HeaderTitle from "@/Components/HeaderTitle";
import { Button } from "@/Components/ui/button";
import { Card, CardContent } from "@/Components/ui/card";
import AppLayout from "@/Layouts/AppLayout";
import { flashMessage } from "@/lib/utils";
import { Link, useForm } from "@inertiajs/react";
import { IconArrowLeft, IconSchool } from "@tabler/icons-react";

export default function Create(props) {

    const { data, setData, post, processing, errors, reset } = useForm({
        faculty_id: null,
        name: '',
        logo: null,
        _method: props.page_settings.method,
    })

    const onHandleChange = (e) => setData(e.target.name, e.target.value);

    const onHandleSubmit = (e) => {
        e.preventDefault();
        post(props.page_settings.action, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (success) => {
                const flash = flashMessage(success);
                if (flash) toast[flash.type](flash.message);
            }
        })
    }

    const onHandleReset = () => reset();

    return (

        <div className="flex flex-col w-full pb-32">

            <div className="flex flex-col items-start justify-between mb-8 gap-y-4 lg:flex-row lg:items-center">
                <HeaderTitle
                    title={props.page_settings.title}
                    subtitle={props.page_settings.subtitle}
                    icon={IconSchool}
                />

                <Button
                    variant='orange'
                    size='xl'
                    className='w-full lg:w-auto'
                    asChild
                >
                    <Link href={route('admin.departments.index')}>
                        <IconArrowLeft className="size-4"/>
                        Kembali
                    </Link>
                </Button>

            </div>

            <Card>
                <CardContent>

                </CardContent>
            </Card>

        </div>
    )

}

Create.layout = (page) => <AppLayout title={page.props.page_settings.title} children={page} />;
